function upload() {
    var f=$("#formFile").prop("files")[0];
    if (f==undefined){
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
    formData.append("file", f);
    xhr.open("POST", url, true);
    xhr.onreadystatechange = function () {
        if (xhr.readyState === 4 && xhr.status === 200) {
            let result = JSON.parse(xhr.responseText);
            if (result.status == "OK") {
                location.reload();
            } else {
                Swal.fire({
                    text: result.error,
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