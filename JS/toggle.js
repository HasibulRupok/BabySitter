function toggleFunction() {
    let button = document.getElementById("toggleDiv");
    button.classList.toggle('toggle');
}

let signOut = () => {
    $.ajax({
        url: "../Helper/logout.php", success: function (value) {
            location.href = "../public/login.php";
        }
    })
}