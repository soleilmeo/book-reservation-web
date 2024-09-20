// JS script for loading user cards
let usercards = document.querySelectorAll("div.usercard");

usercards.forEach(async element => {
    var userid = element.getAttribute("data-uid")
    var response = await fetch(__ROOT+`api/user?id=${userid}`)
    .then((res) => {
        if (res.status !== 200) {
            return null;
        }
        return res.json();
    });

    if (response) {
        element.innerHTML = `<p class="font-italic mb-1" style="font-size:20px;">Submitted by <b>${response["displayName"]}</b></p>
        <a href="${__ROOT}user/${userid}" class="text-secondary mb-0">@${response["username"]}</a>`
    }
});