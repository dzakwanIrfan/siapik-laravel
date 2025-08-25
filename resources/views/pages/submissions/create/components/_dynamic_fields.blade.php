@csrf
<input type="hidden" name="letter_type_id" id="letter_type_id" value="{{ $letterType->intLetterType_ID }}">

@foreach($letterType->letterFields as $field)
  <x-dynamic-field :field="$field" :options="$fieldOptions" />
@endforeach