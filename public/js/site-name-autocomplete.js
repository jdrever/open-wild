let siteName=document.getElementById("siteName");
let siteNameAutocomplete=document.getElementById("siteNameAutocompleteList");

siteName.oninput = function () {

    const userInput = encodeURIComponent(this.value);

    updateUrl='/sites-autocomplete/'+this.value;
    siteNameAutocomplete.innerHTML = "";
    if (userInput.length > 0) {
        fetch(updateUrl).then(function (response) {
            // The API call was successful!
            return response.text();
        }).then(function (html) {
        siteNameAutocomplete.innerHTML =html;
    }).catch(function (err) {
        // There was an error
        console.warn('Something went wrong.', err);
    });
  }}
