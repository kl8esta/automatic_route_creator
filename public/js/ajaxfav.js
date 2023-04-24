/*global $*/
$(function () {
let fav = $('.fav-toggle');  // fav-toggleクラスあるタグを取得

    // いいねボタンをクリック時
    fav.on('click', function () {
        let $this = $(this);  // クリック箇所のタグを取得
        let rtPostId = $this.data('rtpost-id');  // いいねされた投稿IDを取得
        // Ajax通信リクエスト
        $.ajax({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                },
                url: '/ajaxfav',  // ルータの指定
                type: 'POST',
                data: {
                    'rtpost_id': JSON.stringify(rtPostId)
                },  // コントローラに転送(IntがStringになるのを防ぐ)
        })
    
        // Ajax通信成功時
        .done(function (data) {
            // いいねボタン(favedクラス)の切り替え
            $this.toggleClass('faved'); 
    
            // favoritesCountクラスにある、いいね数を更新する
            $this.next('.favoritesCount').html(data.favoritesCount); 
        })
        
        // Ajax通信失敗時
        .fail(function (data, type, err) {
            console.log(data.status)
            console.log(type);
            console.log(err.message);
        });
        
        return false;
    });
});