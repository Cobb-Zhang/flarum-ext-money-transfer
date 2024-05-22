import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend'
import HeaderPrimary from 'flarum/forum/components/HeaderPrimary';

app.initializers.add('cobbz/flarum-ext-money-batch-transfer', () => {
  console.log('[cobbz/flarum-ext-money-batch-transfer] Hello, forum, cobb!');
  // extend(HeaderPrimary.prototype, 'items', function(items) {
  //   items.add('google', <a href="https://google.com">Google</a>);
  // });
});
