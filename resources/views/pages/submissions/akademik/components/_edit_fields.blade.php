@csrf
@method('PUT')
<input type="hidden" name="submission_id" value="{{ $submission->intSubmission_ID }}">

@foreach($letterType->letterFields as $field)
  <x-dynamic-field 
    :field="$field" 
    :options="$fieldOptions" 
    :value="$currentValues[$field->txtFieldName] ?? null" />
@endforeach