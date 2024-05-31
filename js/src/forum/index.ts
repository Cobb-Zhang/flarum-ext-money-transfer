import app from 'flarum/forum/app';
import { extend } from 'flarum/common/extend'
import Extend from 'flarum/common/extenders';
import Button from 'flarum/common/components/Button';
import LinkButton from 'flarum/common/components/LinkButton';
import Model from 'flarum/common/Model';
import Post from 'flarum/common/models/Post';
import UserPage from 'flarum/forum/components/UserPage';
import CommentPost from 'flarum/forum/components/CommentPost';
import PostControls from 'flarum/forum/utils/PostControls';
import extractText from 'flarum/common/utils/extractText';

app.initializers.add('cobbz/flarum-ext-money-transfer', () => {
  console.log('[cobbz/flarum-ext-money-transfer] Hello, forum, cobb4!');
  // extend(HeaderPrimary.prototype, 'items', function(items) {
  //   items.add('google', <a href="https://google.com">Google</a>);
  // });
});
