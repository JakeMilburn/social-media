$(document).ready(function () {

    const posts = document.getElementById('posts');

    if (posts) {
        posts.addEventListener('click', e => {
            if (e.target.className === 'delete-post') {
                if (confirm('Are you sure?')) {
                    const id = e.target.getAttribute('data-id');

                    fetch(`/social-media/public/post/delete/${id}`, {
                        method: 'DELETE'
                    }).then(res => window.location.reload());
                }
            }
        });
    }

    //For liking or disliking a post
    $('.js-like-post').on('click', function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);
        $link.toggleClass('fa-like-o').toggleClass('fa-like');
        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('.post-like-count').html(data.likes);
        })
    });

    //For sending a friend request
    $('.add-friend').on('click', function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('.add-friend').html('Cancel Friend Request');
        })
    });

    //For removing a friend
    $('.remove-friend').on('click', function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);
        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('.remove-friend').html('Add Friend');
        })
    });

    //Loads and shows any pending friend requests
    $('.requests').on('click', function (e) {
        e.preventDefault();
        var $link = $(e.currentTarget);
        // $link.toggleClass('fa-like-o').toggleClass('fa-like');
        $.ajax({
            method: 'POST',
            url: $link.attr('href')
        }).done(function (data) {
            $('#myDropdown').html(data.requests);
            friendAcceptDeclineHandlers();
        });
        document.getElementById("myDropdown").classList.toggle("show");
    });

    //For accepting a friend request
    //Gets called when the friend requests are loaded with ajax
    function friendAcceptDeclineHandlers () {
        $('.accept-request').on('click', function (e) {
            e.preventDefault();
            var $link = $(e.currentTarget);
            // $link.toggleClass('fa-like-o').toggleClass('fa-like');
            $.ajax({
                method: 'POST',
                url: $link.attr('href')
            }).done(function (data) {
                // if (data == 'success') {
                // }
            })
        });

        $('.reject-request').on('click', function (e) {
            e.preventDefault();
            var $link = $(e.currentTarget);
            $.ajax({
                method: 'POST',
                url: $link.attr('href')
            }).done(function (data) {
            })
        });
    }


// Close the dropdown menu if the user clicks outside of it on friend requests panel
    window.onclick = function (event) {
        if (!event.target.matches('.dropbtn')) {
            var dropdowns = document.getElementsByClassName("dropdown-content");
            var i;
            for (i = 0; i < dropdowns.length; i++) {
                var openDropdown = dropdowns[i];
                if (openDropdown.classList.contains('show')) {
                    openDropdown.classList.remove('show');
                }
            }
        }
    };


    friendAcceptDeclineHandlers();

});