<footer>
  <a href="#top" class="text-white" style="text-decoration: none;">©Dream Leaf, All rights reserved.</a>
</footer>

<!-- メニュー用モーダル -->
<div class="modal fade" id="menuModal" tabindex="-1" aria-labelledby="menuModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content rounded-3">
      <div class="modal-header">
        <h5 class="modal-title fw-bold" id="menuModalLabel">メニュー</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="閉じる"></button>
      </div>
      <div class="modal-body text-center">

        <!-- 💡 メニューリスト -->
        <div class="list-group">
          <a href="{{ route('messages.index') }}" class="list-group-item list-group-item-action">
            💬 メッセージ
          </a>
          <a href="{{ route('calendar.index') }}" class="list-group-item list-group-item-action">
            📅 カレンダー
          </a>
          <a href="{{ route('shopping.index') }}" class="list-group-item list-group-item-action">
            🛒 買い物リスト
          </a>
          <a href="{{ route('photos.index') }}" class="list-group-item list-group-item-action">
            📷 写真
          </a>
          <a href="{{ route('videos.index') }}" class="list-group-item list-group-item-action">
            🎥 動画
          </a>
          <a href="{{ route('tasks.index') }}" class="list-group-item list-group-item-action">
            📝 作業リスト
          </a>
        </div>

      </div>
    </div>
  </div>
</div>
