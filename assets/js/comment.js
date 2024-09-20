var CommentEngine = (function() {
    // Should match the ones in comment.php

    let values = {
        loadTime: Date.now(),
        loadOffset: 0,
        commentElement: null,
        moreRepliesTargets: {},
        moreCommentsTargets: {},

        newReplyFields: {},
    }
    
    async function init(values) {
        values.loadTime = Date.now();
        let commentElement = null;
        values.commentElement = document.body.getElementsByClassName("web-comment-container");
        commentElement = values.commentElement;

        if (!commentElement || commentElement === null || commentElement.length <= 0) {
            return false;
        }

        // Initiate first loading of comments
        for (let i = 0; i < commentElement.length; i++) {
            const element = commentElement[i];
            let cid = element.getAttribute("data-comment-id");
            if (cid === null || cid == -1) {
                // Data attribute not assigned
                element.innerHTML = `<p class="text-center text-secondary">This comment section is locked.</p>`;
                return;
            }

            let response = await fetchTimeout(__ROOT+"book/comment?cid="+cid+"&offset=0"+"&loadBeforeDate="+values.loadTime, {
                timeout: 60000
            }).then((res) => {
                if (res === null || (res.status < 200 || res.status > 299)) {
                    return null;
                }
                return res.json();
            }).then((json) => {
                //console.log("HTML output: ", html);
                return json;
            }).catch((err) => {
                element.innerHTML = `<p class="text-center text-secondary">Cannot load comments.</p>`;
            });
            
            if (response === null) {
                element.innerHTML = `<p class="text-center text-secondary">Failed to load comments.</p>`;
                return;
            }

            values.newReplyFields = response.newReplySelectors;
            initialCommentSkip = response.extendedCount;
            element.innerHTML = response.html;

            if (initialCommentSkip == 0) {
                element.innerHTML = `<p class="text-center text-secondary">Be the first to comment.</p>`;
            }

            // Initiate scan for new control elements.
            scan(values, element, initialCommentSkip);
        }

        // Initialize comment deletion handling
        //jquery
        $("#cmt-delete-confirm-modal").on("show.bs.modal", function(e) {
            var target = $(e.relatedTarget).data('comment-id');
            $("#cmt-delete-confirm-target").val(target);
        })

        return true;
    }

    let attachButtonBehaviors = {
        moreReplies: function(values, element, containerElement) {
            // onClick
            element.addEventListener("click", async () => {

                let extendForCommentId = element.getAttribute("data-for");
                if (extendForCommentId === null) {
                    console.log("Data not assigned for one of the morereplies buttons, rendering it unusable");
                    return;
                }
                // Checck if the field for containing replies exist in this instance
                if (!containerElement) {
                    return
                }

                if (!(element.id in values.moreRepliesTargets)) {
                    return;
                }

                let __temp = element.textContent;
                element.setAttribute("disabled", "1");
                element.textContent = "Loading...";

                // Assign target data and then get the offset for seeking replies. After loading, new replyOffset value should be assigned back.
                values.moreRepliesTargets[element.id].targetData = extendForCommentId;
                let replyOffset = values.moreRepliesTargets[element.id].replyOffset;

                let repliesContainers = containerElement.getElementsByClassName("web-replies-container");
                let repliesContainer = null;
                // Choose the first fitting reply container
                for (let i = 0; i < repliesContainers.length; i++) {
                    const _element = repliesContainers[i];
                    let containerForCommentId = _element.getAttribute("data-for");
                    if (extendForCommentId === containerForCommentId) {
                        // Voila
                        repliesContainer = _element;
                    }
                }

                // Check if there are failtexts inside the repliesContainer before reloading
                let failtexts = repliesContainer.getElementsByClassName("web-temp-cmt-failtext");
                for (let i = 0; i < failtexts.length; i++) {
                    const failtext = failtexts[i];
                    failtext.remove();
                }

                // Try fetching more replies
                let response = await fetchTimeout(__ROOT+"book/comment?cid=0"+"&offset="+replyOffset+"&loadBeforeDate=0&replyOf="+extendForCommentId, {
                    timeout: 60000
                }).then((res) => {
                    if (res === null || (res.status < 200 || res.status > 299)) {
                        return null;
                    }
                    return res.json();
                }).then((json) => {
                    console.log("JSON output: ", json);
                    return json;
                }).catch((err) => {
                    repliesContainer.innerHTML += `<p class="web-temp-cmt-failtext text-center text-secondary">Cannot load replies.</p>`;
                });
                
                if (response === null) {
                    repliesContainer.innerHTML += `<p class="web-temp-cmt-failtext text-center text-secondary">Failed to load replies.</p>`;
                    element.textContent = __temp;
                    element.removeAttribute("disabled");
                    return;
                }

                // Success! Now shift those values.
                replyOffset += response.extendedCount - 1;
                values.moreRepliesTargets[element.id].replyOffset = replyOffset;
                console.log(response);
                repliesContainer.innerHTML += response.html;

                // Now let's see if there are more replies ahead. If there's none ahead, delete this element.
                if (!response.isThereMoreComments) {
                    // All clear
                    element.remove();
                } else {
                    // Ready for more
                    element.textContent = __temp;
                    element.removeAttribute("disabled");
                }

                // After this task, scan again for any new elements.
                scan(values, containerElement);
            })
        },

        moreComments: function(values, element, containerElement) {
            // onClick
            element.addEventListener("click", async () => {
                let cid = element.getAttribute("data-for");
                if (cid === null) {
                    console.log("Data not assigned for one of the morecomments buttons, rendering it unusable");
                    return;
                }
                // Checck if the field for containing replies exist in this instance
                if (!containerElement) {
                    return
                }

                if (!(element in values.moreCommentsTargets)) {
                    return;
                }

                let __temp = element.textContent;
                element.setAttribute("disabled", "1");
                element.textContent = "Loading...";

                // Assign target data and then get the offset for seeking replies. After loading, new commentOffset value should be assigned back.
                values.moreCommentsTargets[element].targetData = cid;
                let commentOffset = values.moreCommentsTargets[element].commentOffset;

                // Container element will be used for comment storage
                // Check if there are failtexts inside the repliesContainer before reloading
                let failtexts = containerElement.getElementsByClassName("web-temp-cmt-failtext");
                for (let i = 0; i < failtexts.length; i++) {
                    const failtext = failtexts[i];
                    failtext.remove();
                }

                // Try fetching more replies
                let response = await fetchTimeout(__ROOT+"book/comment?cid="+cid+"&offset="+commentOffset+"&loadBeforeDate="+values.loadTime, {
                    timeout: 60000
                }).then((res) => {
                    if (res === null || (res.status < 200 || res.status > 299)) {
                        return null;
                    }
                    return res.json();
                }).then((json) => {
                    console.log("JSON output: ", json);
                    return json;
                }).catch((err) => {
                    containerElement.innerHTML += `<p class="web-temp-cmt-failtext text-center text-secondary">Cannot load comments.</p>`;
                });
                
                if (response === null) {
                    containerElement.innerHTML += `<p class="web-temp-cmt-failtext text-center text-secondary">Failed to load comments.</p>`;
                    element.textContent = __temp;
                    element.removeAttribute("disabled");
                    return;
                }

                // Success! Now shift those values.
                values.newReplyFields = response.newReplySelectors;
                commentOffset += response.extendedCount - 1;
                values.moreCommentsTargets[element].commentOffset = commentOffset;
                //console.log(response);
                element.insertAdjacentHTML("beforebegin", response.html); 

                // Now let's see if there are more replies ahead. If there's none ahead, delete this element.
                if (!response.isThereMoreComments) {
                    // All clear
                    //console.log("Removing...");
                    element.remove();
                } else {
                    // Ready for more
                    //console.log("Re-readying...");
                    element.textContent = __temp;
                    element.removeAttribute("disabled");
                }

                // After this task, scan again for any new elements.
                scan(values, containerElement);
            })
        }
    }

    function scan(values, containerElement, initialCommentSkip = 0) {
        let moreRepliesButton = containerElement.getElementsByClassName("web-btn-morereplies");
        let moreRepliesCount = Object.keys(values.moreRepliesTargets).length;
        for (let i = 0; i < moreRepliesButton.length; i++) {
            const element = moreRepliesButton[i];
            if (!element.id) {
                element.id = "__btn_morereplies_cmt_" + moreRepliesCount
                moreRepliesCount++;
            }
            if (element.id in values.moreRepliesTargets) {
                // Will not repeat attaching behavior to this element.
                console.log("already here")
                continue;
            }
            // Initiate a new dictionary storing reply offsets
            values.moreRepliesTargets[element.id] = {
                targetData: -1,
                replyOffset: 0,
            }
            attachButtonBehaviors.moreReplies(values, element, containerElement);
        }

        // Initiate a new dictionary storing comments offsets
        let moreCommentsButton = containerElement.getElementsByClassName("web-btn-morecomments");
        for (let i = 0; i < moreCommentsButton.length; i++) {
            const element = moreCommentsButton[i];
            if (element in values.moreCommentsTargets) {
                // Will not repeat attaching behavior to this element.
                continue;
            }
            // Initiate a new dictionary storing comment offsets
            values.moreCommentsTargets[element] = {
                targetData: -1,
                commentOffset: initialCommentSkip - 1,
            }
            attachButtonBehaviors.moreComments(values, element, containerElement);
        }

        // Check for any new reply fields
        for (let i = 0; i < values.newReplyFields.length; i++) {
            const replyFieldId = values.newReplyFields[i];
            // jquery
            let form = $("#"+replyFieldId);
            let btn = $("#"+replyFieldId+" .web-reply-submit-button");
            let btntooltip = $("#"+replyFieldId+" .web-reply-submit-button .far");
            let txtarea = $("#"+replyFieldId+" textarea");
            if (!(form && btn && txtarea)) {
                console.log("Form missing submission driver");
                return;
            }

            btn.click(function() {
                if (!$.trim(txtarea.val())) {
                    txtarea.addClass("web-red-textbox-border");
                    setTimeout(
                        function() {
                            txtarea.removeClass("web-red-textbox-border")
                        },
                        1500
                    )
                    return;
                }

                btn.prop("disabled", true);
                btntooltip.removeClass("fa-comment").addClass("fa fa-spinner fa-pulse");
                var url = __ROOT+"book/reply"; // Form input handler
            
                $.ajax({
                    type: "POST",
                    url: url,
                    data: form.serialize(), // Serializes the form's elements
                    success: function(data)
                    {
                    txtarea.val("");
                    btn.addClass("btn-success");
                    btn.removeClass("btn-primary");
                    btntooltip.removeClass("fa-spinner fa-pulse").addClass("fa-check");
                    setTimeout(
                        function() {
                            btn.prop("disabled", false);
                            btn.removeClass("btn-success");
                            btn.addClass("btn-primary");
                            btntooltip.removeClass("fa fa-check").addClass("fa-comment")
                        },
                        1500
                    )
                    }
                });
                
                
                return false; // Avoid executing the actual submit of the form.
            });
        }

        values.newReplyFields = {};
    }

    return {
        //v
        values: values,
        //f
        init: init,
        scan: scan,
    }
}())

let engineInitResult = CommentEngine.init(CommentEngine.values);
if (!engineInitResult) {
    console.log("CommentEngine failed to load. Is comment element defined?");
}