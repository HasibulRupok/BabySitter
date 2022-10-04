
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

//message send button
function knockPressed() {
    let divs = document.getElementsByClassName('hiddeMessageDivClass');
    // console.log(div);
    for (x of divs) {
        x.classList.toggle('toggle2');
    }
}


// profile clicked
function profileClick() {
    // console.log("Profile clicked");
    location.href = "../Pages/profile.php";
}


