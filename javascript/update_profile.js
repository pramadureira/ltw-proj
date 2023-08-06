const inpFile = document.getElementById("profile-input")

if (inpFile != null) {
    const profPicPreview = document.querySelector("div#photo > img")
    inpFile.addEventListener("change", function() {
        const file = this.files[0]
        if (file) {
            const reader = new FileReader()
            reader.addEventListener("load", function() {
                profPicPreview.setAttribute("src", this.result)
            });

            reader.readAsDataURL(file)
        }
    })
}
