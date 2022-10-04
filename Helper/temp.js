let knockButtonClicked = () => {
    let hiddenDIv = document.getElementById('hiddenDiv');
    hiddenDIv.style.display = 'block';
}

let sendPressed = () => {
    console.log("Clicked");
    let hiddenDIv = document.getElementById('hiddenDiv');
    hiddenDIv.style.display = 'none';
}