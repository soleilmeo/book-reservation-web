//jquery
async function InitializeBookRating() {
    let book_like_btn = $("#book-like-btn");
    let book_dislike_btn = $("#book-dislike-btn");
    let book_like_counter = $("#book-like-count");
    let book_dislike_counter = $("#book-dislike-count");
    let book_like_count = 0
    let book_dislike_count = 0
    let didUserLikedOrDisliked = 0 // -1 = undecided, 0 - disliked, 1 - liked
    let initialDecision = 0
    
    if (__BOOK_ID === undefined || __BOOK_ID === null || !__BOOK_ID) return false;
    console.log("Pass");

    // Request
    var success = false;
    let res = null;
    res = await $.get(__ROOT+"api/book?id="+__BOOK_ID+"&ratingOnly=",
        function (data, status) {
            console.log(data);
            console.log(status);
            if (status == "success") {
                success = true;
                book_like_count = parseInt(data.likes);
                book_dislike_count = parseInt(data.dislikes);
                didUserLikedOrDisliked = data.userLiked;
                initialDecision = didUserLikedOrDisliked;
            }
        }
    ).promise();
    if (!success) return false;
    console.log("Pass 2");

    let updateCounter = function(newlikes, newdislikes) {
        book_like_counter.text(newlikes);
        book_dislike_counter.text(newdislikes);
    }

    updateCounter(book_like_count, book_dislike_count);
    if (didUserLikedOrDisliked >= 0) {
        if (didUserLikedOrDisliked == 0) {
            book_dislike_btn.addClass("rated");
        } else {
            book_like_btn.addClass("rated");
        }
    }

    book_dislike_btn.click(function() {
        console.log("Clicked");
        if (book_dislike_btn.is(":disabled")) return;
        book_dislike_btn.prop("disabled", true);

        let decidedRating = 0;
        console.log(didUserLikedOrDisliked)
        if (didUserLikedOrDisliked == 0) {
            decidedRating = -1;
        }

        $.ajax(__ROOT+"book/rate", {
            type: "POST",
            data: {
                book_rating: decidedRating,
                book_id: __BOOK_ID
            },
            statusCode: {
                403: function(response) {
                    window.location.href(__ROOT+"login");
                }
            },
            success: function (data) {
                if (data != decidedRating) {
                    // Error
                    console.warn("Error occurred while submitting rating");
                } else {
                    book_like_btn.removeClass("rated");
                    book_dislike_btn.removeClass("rated");
                    if (data == 0) {
                        book_dislike_btn.addClass("rated");
                        if (initialDecision == 1) {
                            updateCounter(book_like_count - 1, book_dislike_count + 1);
                        } else if (initialDecision == 0) {
                            updateCounter(book_like_count, book_dislike_count);
                        } else updateCounter(book_like_count, book_dislike_count + 1);
                    } else {
                        if (initialDecision == 1) {
                            updateCounter(book_like_count - 1, book_dislike_count);
                        } else if (initialDecision == 0) {
                            updateCounter(book_like_count, book_dislike_count - 1);
                        } else updateCounter(book_like_count, book_dislike_count);
                    }

                    didUserLikedOrDisliked = data;
                    book_dislike_btn.prop("disabled", false);
                }
            }
        });
        return;
    });

    book_like_btn.click(function() {
        if (book_like_btn.is(":disabled")) return;
        book_like_btn.prop("disabled", true);

        let decidedRating = 1;
        if (didUserLikedOrDisliked == 1) {
            decidedRating = -1;
        }

        $.ajax(__ROOT+"book/rate", {
            type: "POST",
            data: {
                book_rating: decidedRating,
                book_id: __BOOK_ID
            },
            statusCode: {
                403: function(response) {
                    window.location.href(__ROOT+"login");
                }
            },
            success: function (data) {
                if (data != decidedRating) {
                    // Error
                    console.warn("Error occurred while submitting rating");
                } else {
                    book_like_btn.removeClass("rated");
                    book_dislike_btn.removeClass("rated");
                    if (data == 1) {
                        book_like_btn.addClass("rated");
                        if (initialDecision == 0) {
                            updateCounter(book_like_count + 1, book_dislike_count - 1);
                        } if (initialDecision == 1) {
                            updateCounter(book_like_count, book_dislike_count);
                        } else updateCounter(book_like_count + 1, book_dislike_count);
                    } else {
                        if (initialDecision == 1) {
                            updateCounter(book_like_count - 1, book_dislike_count);
                        } else if (initialDecision == 0) {
                            updateCounter(book_like_count, book_dislike_count - 1);
                        } else updateCounter(book_like_count, book_dislike_count);
                    }

                    didUserLikedOrDisliked = data;
                    book_like_btn.prop("disabled", false);
                }
            }
        });
        return;
    })
}

InitializeBookRating();