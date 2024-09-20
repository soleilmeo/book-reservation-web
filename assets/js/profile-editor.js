let _profilePicOriginalPreview_lnk = null;

function avtRevertEvent() {
    var input = document.getElementById('p-pfp');
    var feedback = document.getElementById('avatar-feedback');
    var preview = document.getElementById('p-pfp-preview');

    if (!input.files) {
        console.error("Missing features on an unsupported browser. Operation cannot be done.");
    } else {
        var file = input.files[0];
        if (file) {
            if (_profilePicOriginalPreview_lnk !== null) preview.src = _profilePicOriginalPreview_lnk;
            else preview.src = __ROOT + "assets/picture/pfp-default.png";
            input.value = "";
            feedback.innerHTML = '<i class="fa fa-question-circle mt-1"></i> No new avatar selected';
        }
    }
}

function avtRevertInitListener() {
    var rvt = document.getElementById("avt-revert");
    if (rvt) {
        rvt.removeEventListener("click", avtRevertEvent);
        rvt.addEventListener("click", avtRevertEvent);
    }
}

document.getElementById("p-pfp").addEventListener("change", function () {
    if (!window.FileReader) {
        console.log("The file API is unsupported on this browser for some reasons.");
        return;
    }

    var input = document.getElementById('p-pfp');
    var feedback = document.getElementById('avatar-feedback');
    var preview = document.getElementById('p-pfp-preview');

    if (_profilePicOriginalPreview_lnk === null) {
        _profilePicOriginalPreview_lnk = preview.src;
    }

    if (!input.files) {
        console.error("Missing features on an unsupported browser. Operation cannot be done.");
    } else if (!input.files[0]) {
        feedback.innerHTML = '<i class="fa fa-question-circle mt-1"></i> No new avatar selected';
    } else {
        var file = input.files[0];
        if (file.size > 5242880) {
            input.value = "";
            feedback.innerHTML = '<span class="text-danger"><i class="fa fa-times mt-1"></i> File must be less than 5MB</span>';
        } else {
            preview.src = URL.createObjectURL(file);
            feedback.innerHTML = '<span class="text-success"><i class="fa fa-check mt-1"></i> Avatar OK </span><a id="avt-revert" class="badge badge-info user-select-none"><i class="fa fa-undo"></i></a>';
            avtRevertInitListener();
        }
    }
});

if (typeof(__profileEditReport) === 'object') {
    if (__profileEditReport.invoked) {
        if (__profileEditReport.success) {
            $.showNotification({
                body: "Successfully updated profile.",
                type: "success",
                duration: 5000
            })
        } else {
            if (__profileEditReport.err.length > 0) {
                $.showNotification({
                    body: __profileEditReport.err,
                    type: "danger",
                    duration: 5000
                })
            } else {
                $.showNotification({
                    body: "Unknown Error",
                    type: "danger",
                    duration: 5000
                })
            }
        }
    }
}

/*
let _SAVEBTN_clicked = false;
$("#save_btn").click(async (e) => {
    if (_SAVEBTN_clicked) return;

    _SAVEBTN_clicked = true;
    await $.ajax({
        type: "POST",
        url: __ROOT+"profile/edit",
        data: $("#profile-editor-form").serialize(),
        statusCode: {
            403: function (response) {
                $.showNotification({
                    body: "Access denied.",
                    type: "danger",
                    duration: 3000
                })
            },
            404: function(response) {
                $.showNotification({
                    body: "Cannot update profile. Please check your internet connection.",
                    type: "danger",
                    duration: 3000
                })
            }
        },
        success: function (response) {
            if (response == "success") {
                $.showNotification({
                    body: "Successfully updated profile.",
                    type: "success",
                    duration: 2500
                })
            } else {
                $.showNotification({
                    body: response,
                    type: "danger",
                    duration: 3000
                })
            }
        }
    });

    _SAVEBTN_clicked = false;
    return;
});
*/