// GENERAL RULE OF THUMB FOR IMAGES
/*

- When an image exists on the server, it usually loads in first with a link to the image in the server. Save it in an associative array.
- After modifications, check if the image link is emptied or modified. If it is, it should be comsidered "Deleted".
- Locally selected image from the host machine that hasn't been uploaded to the server will not be affected.

*/

const __Q_LOCKDOWN = true;

// jquery
function isNotBlank(v) {
    return typeof v !== 'undefined' && v !== null && v !== ""
}

// Handling saving and book deletion

// Initialize book deletion handling
//jquery
let __qsave_waiting = false;
let masterform = $("#bookeditor-master-form");
let qsvbtn = $("#qsave_btn");

let save_warningmodal = $("#book-save-modal");
let save_confirmmodal_btn = $("#btn-confirm-book-save");
let has_save_warning_on_privacy = false;
let save_warning_accepted = false;

if (save_warningmodal.length) {
    has_save_warning_on_privacy = true
}

masterform.dirty({preventLeaving: true});

if (save_confirmmodal_btn.length) {
    save_confirmmodal_btn.click(function() {
        save_warning_accepted = true
        if (save_warningmodal.length) {
            save_warningmodal.modal("hide")
        }
        qsvbtn.trigger("click");
    })
}

qsvbtn.click(function() {
    if (__qsave_waiting === true) return;

    let editor_master_form_save = $("#bookeditor-master-form").serializeArray().reduce(function(obj, item) {
        obj[item.name] = item.value;
        return obj;
    }, {});
    if (has_save_warning_on_privacy) {
        if (!save_warning_accepted) {
            if (editor_master_form_save["book_publicity"] == 0) {
                save_warningmodal.modal('show')
                return
            }
        }
    }

    let oldtxt = qsvbtn.html();
    qsvbtn.html('<i class="fa fa-soubber fa-pulse mr-1" aria-hidden="true"></i> ' + "Saving...")
    qsvbtn.prop("disabled", true)
    
    __qsave_waiting = true;
    
    $.ajax({
        type: "POST",
        url: __ROOT+"book/edit",
        data: $("#bookeditor-master-form").serialize(), // Serializes the form's elements
        statusCode: {
            403: function(response) {
                window.location.href(__ROOT + "login");
            }
        },
        success: function(data)
        {
            if (data !== "success") {
                $("#qerror-displayer").text(data);
                qsvbtn.html('<i class="fa fa-exclamation mr-1" aria-hidden="true"></i> ' + "Failed to save");
                window.location.replace("#qerror-displayer");
            } else {
                masterform.dirty("setAsClean");
                qsvbtn.html('<i class="fa fa-check mr-1" aria-hidden="true"></i>' + "Saved!");
            }
            setTimeout(
                function() {
                    qsvbtn.html(oldtxt);
                    qsvbtn.prop("disabled", false)

                    if (has_save_warning_on_privacy && save_warning_accepted) {
                        save_warning_accepted = false;
                        has_save_warning_on_privacy = false;
                    }
                    __qsave_waiting = false;
                },
                1000
            )
        }
    });

    return;  
})

$("#book-delete-modal").on("show.bs.modal", function(e) {
    var target = $(e.relatedTarget).data('book-id');
    $("#book-delete-confirm-target").val(target);
})

$("#btn-confirm-book-delete").click(function() {
    masterform.dirty("setAsClean");
})

