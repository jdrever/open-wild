let speciesName=document.getElementById("speciesName");
let speciesNameAutocomplete=document.getElementById("speciesNameAutocompleteList");
console.log(speciesName);

speciesName.oninput = function () {

    const userInput = encodeURIComponent(this.value);
    console.log(userInput);
    updateUrl='/species-autocomplete/'+this.value;
    speciesNameAutocomplete.innerHTML = "";
    if (userInput.length > 2) {
        fetch(updateUrl).then(function (response) {
            // The API call was successful!
            return response.text();
        }).then(function (html) {
        speciesNameAutocomplete.innerHTML =html;
        speciesName.setAttribute('list','speciesNameAutocompleteList');
    }).catch(function (err) {
        // There was an error
        console.warn('Something went wrong.', err);
    });
  }}
