function listenForRefreshClicks()
{
document.querySelectorAll("[data-refresh='true']").forEach(item => {
    item.addEventListener('click', event => {
        event.preventDefault();
        pageNumber=item.hasAttribute('data-page') ? item.dataset.page : 1;
        updateDataset(pageNumber);
    })
  })
}

listenForRefreshClicks();

function updateDataset(pageNumber)
{
console.log("showSpinner");
showSpinner();
updateUrl=getUpdateUrl(pageNumber);
console.log(updateUrl);
fetch(updateUrl).then(function (response) {
	console.log("The API call was successful!");
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
    listenForRefreshClicks();
}).catch(function (err) {
	// There was an error
	console.warn('Something went wrong.', err);
});
}

function showSpinner() {
    var elem = document.querySelector('#data-table');
    elem.innerHTML = '<div class="text-center"><button class="btn btn-primary" type="button" disabled><span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span> Loading... </button></div>';
}



