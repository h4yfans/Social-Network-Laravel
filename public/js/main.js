/**
 * Created by Kaan on 9/23/2016.
 */

var postID = 0;
var postBodyElement = null;

$('.post').find('.interaction').find('.edit').on('click', function (event) {
    event.preventDefault();
    postBodyElement = event.target.parentNode.parentNode.childNodes[1];
    var postBody = postBodyElement.textContent;
    console.log('postBody -> ' + postBody);
    console.log('postBodyElement -> ' + $(postBodyElement).text());
    postID = event.target.parentNode.parentNode.dataset['postid'];

    $('#post-body').val(postBody);
    $('#edit-modal').modal();
});

$('#modal-save').on('click', function () {
    $.ajax({
        method: 'POST',
        url: urlEdit,
        data: {
            body: $('#post-body').val(),
            postId: postID,
            _token: token
        }
    }).done(function (msg) {
        $(postBodyElement).text(msg['new_body']);
        $('#edit-modal').modal('hide');
    });
});


$('.like').on('click', function (event) {
    event.preventDefault();
    postID = event.target.parentNode.parentNode.dataset['postid'];
    var isLike = event.target.previousElementSibling == null;
    $.ajax({
        method: 'POST',
        url: urlLike,
        data: {
            isLike: isLike,
            postId: postID,
            _token: token
        }
    }).done(function () {
        console.log(isLike);
        event.target.innerText = isLike ? event.target.innerText == 'Like' ? 'You like this post' : 'Like' : event.target.innerText == 'Dislike' ? 'You don\'t like this post' : 'Dislike';
        if (isLike){
            event.target.nextElementSibling.innerText = 'Dislike';
        }else{
            event.target.previousElementSibling.innerText = 'Like';
        }
    });
});