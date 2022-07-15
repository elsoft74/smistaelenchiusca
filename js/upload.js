function upload() {
    f=$("#formFile").prop("files");
    if (f.length==0){
        Swal.fire({
            text: "Nessun file da dividere.",
            icon: 'error',
            showCancelButton: false,
            confirmButtonColor: '#3085d6',
            confirmButtonText: 'Ok'
        });
    }
    let xhr = new XMLHttpRequest();
    let url = "be/upload.php";
    var formData = new FormData();
    formData.append("file", f[0]);
    xhr.open("POST", url, true);
    xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let result = JSON.parse(xhr.responseText);
            if (result.status == "OK") {
                console.log(result.data);
            } else {
                Swal.fire({
                    text: result.debug,
                    icon: 'error',
                    showCancelButton: false,
                    confirmButtonColor: '#3085d6',
                    confirmButtonText: 'Ok'
                });
            }
        }
    }
    xhr.send(formData);
}