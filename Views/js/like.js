//////////////////////
////// いいねを作る！
///////////////////////

$(function(){
    //いいねがクリックされた時
    $('.js-like').click(function(){
        const this_obj = $(this);
        const like_id = $(this).data('like-id');
        const like_count_obj = $(this).parent().find('.js-like-count');
        let like_count = Number(like_count_obj.html());

        if(like_id){
            //いいね取り消し
            like_count--;
            like_count_obj.html(like_count);
            this_obj.data('like-id',null);

            //いいねのハートをグレーに変える
            $(this).find('img').attr('src','../Views/img/icon-heart.svg');
        }else{
            //いいね付与
            like_count++;
            like_count_obj.html(like_count);
            this_obj.data('like-id',true);

            //いいねを青に変える

            $(this).find('img').attr('src','../Views/img/icon-heart-twitterblue.svg');

        }
    });
})