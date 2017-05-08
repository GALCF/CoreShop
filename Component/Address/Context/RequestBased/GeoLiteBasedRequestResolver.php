<?php
/**
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
*/

namespace CoreShop\Component\Address\Context\RequestBased;

use CoreShop\Component\Address\Context\CountryNotFoundException;
use CoreShop\Component\Address\Model\CountryInterface;
use CoreShop\Component\Address\Repository\CountryRepositoryInterface;
use GeoIp2\Database\Reader;
use Symfony\Component\HttpFoundation\Request;

final class GeoLiteBasedRequestResolver implements RequestResolverInterface
{
    /**
     * @var CountryRepositoryInterface
     */
    private $countryRepository;

    public function __construct(CountryRepositoryInterface $countryRepository)
    {
        $this->countryRepository = $countryRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function findCountry(Request $request)
    {
        $geoDbFile = realpath(PIMCORE_PRIVATE_VAR.'/config/GeoLite2-City.mmdb');
        $record = null;

        if (file_exists($geoDbFile)) {
            try {
                $reader = new Reader($geoDbFile);

                if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
                    $ip = $_SERVER['HTTP_CLIENT_IP'];
                } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
                    $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
                } else {
                    $ip = $_SERVER['REMOTE_ADDR'];
                }

                if (!$this->checkIfIpIsPrivate($ip)) {
                    $record = $reader->city($ip);

                    $country = $this->countryRepository->findByCode($record->country->isoCode);

                    if ($country instanceof CountryInterface) {
                        return $country;
                    }
                }
            } catch (\Exception $e) {
            }
        }

        throw new CountryNotFoundException();
    }

    /**
     * Check if ip is private.
     *
     * @param $ip
     *
     * @return bool
     */
    private function checkIfIpIsPrivate($ip)
    {
        $pri_addrs = [
            '10.0.0.0|10.255.255.255', // single class A network
            '172.16.0.0|172.31.255.255', // 16 contiguous class B network
            '192.168.0.0|192.168.255.255', // 256 contiguous class C network
            '169.254.0.0|169.254.255.255', // Link-local address also refered to as Automatic Private IP Addressing
            '127.0.0.0|127.255.255.255', // localhost
        ];

        $long_ip = ip2long($ip);
        if ($long_ip != -1) {
            foreach ($pri_addrs as $pri_addr) {
                list($start, $end) = explode('|', $pri_addr);

                // IF IS PRIVATE
                if ($long_ip >= ip2long($start) && $long_ip <= ip2long($end)) {
                    return true;
                }
            }
        }

        return false;
    }
}
