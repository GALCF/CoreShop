/*
 * CoreShop.
 *
 * This source file is subject to the GNU General Public License version 3 (GPLv3)
 * For the full copyright and license information, please view the LICENSE.md and gpl-3.0.txt
 * files that are distributed with this source code.
 *
 * @copyright  Copyright (c) 2015-2017 Dominik Pfaffenbauer (https://www.pfaffenbauer.at)
 * @license    https://www.coreshop.org/license     GNU General Public License version 3 (GPLv3)
 *
 */

pimcore.registerNS('coreshop.order.order.editInvoice');
coreshop.order.order.editInvoice = {

    showWindow: function (invoice, currency, callback) {
        var window = new Ext.window.Window({
            width: 600,
            height: 450,
            resizeable: false,
            layout: 'fit',
            items: [{
                xtype: 'form',
                bodyStyle: 'padding:20px 5px 20px 5px;',
                border: false,
                autoScroll: true,
                forceLayout: true,
                fieldDefaults: {
                    labelWidth: 150
                },
                buttons: [
                    {
                        text: t('save'),
                        handler: function (btn) {
                            var form = btn.up('window').down('form').getForm();

                            if (form.isValid()) {
                                var formValues = form.getFieldValues();

                                formValues['id'] = invoice.get('o_id');

                                Ext.Ajax.request({
                                    url: '/admin/coreshop/order-invoice/update-invoice',
                                    method: 'post',
                                    params: formValues,
                                    callback: function (request, success, response) {
                                        try {
                                            response = Ext.decode(response.responseText);

                                            if (response.success === true) {
                                                window.close();
                                                window.destroy();

                                                if (callback) {
                                                    callback(response);
                                                }
                                            } else if(response.success === false) {
                                                Ext.Msg.alert(t('error'), response.message);
                                            }
                                        }
                                        catch (e) {
                                            Ext.Msg.alert(t('error'), e);
                                        }
                                    }
                                });
                            }
                        },

                        iconCls: 'pimcore_icon_apply'
                    }
                ],
                items: [
                    {
                        xtype: 'datefield',
                        format: 'd.m.Y H:i',
                        altFormats: 'U',
                        fieldLabel: t('coreshop_date'),
                        name: 'date',
                        disabled: true,
                        value: invoice.get('invoiceDate')
                    },
                    {
                        xtype: 'textfield',
                        fieldLabel: t('coreshop_invoice_number'),
                        disabled: true,
                        value: invoice.get('invoiceNumber')
                    },
                    {
                        xtype: 'textfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_total_without_tax'),
                        disabled: true,
                        value: invoice.get('totalNet') / 100,
                        renderer: coreshop.util.format.currency.bind(this, currency.symbol)
                    },
                    {
                        xtype: 'textfield',
                        name: 'amount',
                        fieldLabel: t('coreshop_total'),
                        disabled: true,
                        value: invoice.get('totalGross') / 100,
                        renderer: coreshop.util.format.currency.bind(this, currency.symbol)
                    },
                    {
                        xtype: 'button',
                        fieldLabel: '',
                        style: 'margin: 5px 0;',
                        tooltip: t('open'),
                        handler: function (widgetColumn) {
                            pimcore.helpers.openObject(invoice.get('o_id'), 'object');
                        },
                        listeners: {
                            beforerender: function (widgetColumn) {
                                widgetColumn.setText(Ext.String.format(t('coreshop_open_order_invoice'), invoice.get('invoiceNumber')));
                            }
                        }
                    },
                    {
                        xtype: 'gridpanel',
                        title: t('details'),
                        store: new Ext.data.ArrayStore({
                            data: invoice.get('details'),
                            fields: ['name', 'value']
                        }),
                        columns: [
                            {text: 'Name', dataIndex: 'name', flex: 1 },
                            {text: 'Value', dataIndex: 'value', flex: 2 }
                        ]
                    }
                ]
            }]
        });

        window.show();
    }

};
