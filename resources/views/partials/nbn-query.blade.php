<?php if (isset($results->queryUrl)) { ?>
    <details style="font-size:small;"><summary>NBN API Query</summary>{{ $results->queryUrl }}
    <a href="https://jsonformatter.curiousconcept.com/?data={{ $results->queryUrl }}&process=true" target="_blank">View as JSON</a>
    </details>
<?php } ?>
