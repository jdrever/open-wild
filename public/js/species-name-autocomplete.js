let speciesName=document.getElementById("speciesName");
let speciesNameAutocomplete=document.getElementById("speciesNameAutocompleteList");
console.log(speciesName);

speciesName.oninput = function () {

    if (this.value.startsWith("Scientific Name:"))
    {
        document.getElementById('speciesNameTypeScientific').checked=true;
        speciesName.value=this.value.replace("Scientific Name: ","");
        return;
    }

    if (this.value.startsWith("Common Name:"))
    {
        document.getElementById('speciesNameTypeCommon').checked=true;
        speciesName.value=this.value.replace("Common Name: ","");
        return;
    }
    const userInput = encodeURIComponent(this.value);

    updateUrl='/species-autocomplete/'+this.value;
    speciesNameAutocomplete.innerHTML = "";
    if (userInput.length > 0) {
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
