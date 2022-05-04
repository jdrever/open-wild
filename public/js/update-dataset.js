

function updateDataset(pageNumber)
{
showSpinner();
updateUrl=getUpdateUrl(pageNumber);
fetch(updateUrl).then(function (response) {
	// The API call was successful!
	return response.text();
}).then(function (html) {
    var elem = document.querySelector('#data-table');

    //Set HTML content
    elem.innerHTML = html;
    if (document.getElementById('map-container'))
    {
        const map = initialiseBasicMap();
        updateMarker();
    }

}).catch(function (err) {
	// There was an error
	console.warn('Something went wrong.', err);
});
}

function showSpinner() {
    var elem = document.querySelector('#data-table');
    elem.innerHTML = '<div class="text-center"><button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading... </button></div>';
}

