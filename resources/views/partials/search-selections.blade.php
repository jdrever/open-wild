<div class="row justify-content-center gy-3">
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="speciesNameTypeScientific" value="scientific"  data-refresh="true" {{ ($speciesNameType=="scientific")? "checked" : "" }} />
			<label class="form-check-label" for="scientific-name">
				scientific<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesNameType" id="speciesNameTypeCommon" value="common" data-refresh="true" {{ ($speciesNameType=="common")? "checked" : "" }} />
			<label class="form-check-label" for="common-name">
				common<span class="d-none d-lg-inline"> name only</span>
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="checkbox" name="axiophyteFilter" id="axiophyteFilter" value="true" data-refresh="true"{{ ($axiophyteFilter=="true")? "checked" : "" }} />
			<label class="form-check-label" for="axiophyte-name">
				<span class="d-lg-none">axiophytes</span>
				<span class="d-none d-lg-inline">axiophytes only</span>
			</label>
		</div>
	</div>
	<div class="form-group col-sm-4 col-lg-3">
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="plants" data-refresh="true" {{ ($speciesGroup=="plants")? "checked" : "" }} />
			<label class="form-check-label" for="plants">
				only plants
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="bryophytes" data-refresh="true" {{ ($speciesGroup=="bryophytes")? "checked" : "" }} />
			<label class="form-check-label" for="bryophytes">
				only bryophytes
			</label>
		</div>
		<div class="form-check">
			<input class="form-check-input" type="radio" name="speciesGroup" id="speciesGroup" value="both"  data-refresh="true" {{ ($speciesGroup=="both")? "checked" : "" }} />
			<label class="form-check-label" for="both">
				both <span class="d-none d-xl-inline">plants and bryophytes</span>
			</label>
		</div>
	</div>
</div>
