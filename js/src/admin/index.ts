import app from 'flarum/admin/app';

app.initializers.add('cobbz/flarum-ext-money-transfer', () => {
  app.extensionData
    .for('cobbz-money-transfer')
    .registerSetting({
      setting: 'cobbz-money-transfer.transfer-amount',
      label: app.translator.trans('cobbz-money-transfer.admin.transfer-amount.label'),
      help: app.translator.trans('cobbz-money-transfer.admin.transfer-amount.help'),
      type: 'number',
    }, 30)
    .registerSetting({
      type: 'string',
      setting: 'cobbz-money-transfer.moneyTransferTimeZone',
      label: app.translator.trans('cobbz-money-transfer.admin.transfer-money-timezone'),
      help: app.translator.trans('cobbz-money-transfer.admin.transfer-money-timezone-help'),
      placeholder: app.translator.trans('cobbz-money-transfer.admin.transfer-money-timezone-default')
    })
    .registerPermission(
      {
        icon: 'fas fa-money-bill',
        label: app.translator.trans('cobbz-money-transfer.admin.permissions.edit_money_label'),
        permission: 'cobbz-money-transfer.allowUseTranferMoney',
      },
      'moderate',
    );
});
