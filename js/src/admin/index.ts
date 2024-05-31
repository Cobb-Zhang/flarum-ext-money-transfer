import app from 'flarum/admin/app';

app.initializers.add('cobbz/flarum-ext-money-batch-transfer', () => {
  app.extensionData
    .for('cobbz-money-batch-transfer')
    .registerSetting({
      setting: 'cobbz-money-batch-transfer.transfer-amount',
      label: app.translator.trans('cobbz-money-batch-transfer.admin.transfer-amount.label'),
      help: app.translator.trans('cobbz-money-batch-transfer.admin.transfer-amount.help'),
      type: 'number',
    }, 30)
    .registerSetting({
      type: 'string',
      setting: 'cobbz-money-batch-transfer.moneyTransferTimeZone',
      label: app.translator.trans('cobbz-money-batch-transfer.admin.transfer-money-timezone'),
      help: app.translator.trans('cobbz-money-batch-transfer.admin.transfer-money-timezone-help'),
      placeholder: app.translator.trans('cobbz-money-batch-transfer.admin.transfer-money-timezone-default')
    })
    .registerPermission(
      {
        icon: 'fas fa-money-bill',
        label: app.translator.trans('cobbz-money-batch-transfer.admin.permissions.edit_money_label'),
        permission: 'cobbz-money-batch-transfer.allowUseTranferMoney',
      },
      'moderate',
    );
});
