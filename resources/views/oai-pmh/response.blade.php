@php echo '<?xml version="1.0" encoding="UTF-8"@endphp'; ?>
@php
    $requestAttributeString = '';

    foreach ($requestAttributes as $key => $value) {
        $requestAttributeString .= ' ' . $key . '="' . e($value) . '"';
    }
@endphp
<OAI-PMH xmlns="http://www.openarchives.org/OAI/2.0/" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
    xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/ http://www.openarchives.org/OAI/2.0/OAI-PMH.xsd">
    <responseDate>{{ $responseDate }}</responseDate>
    <request{!! $requestAttributeString !!}>{{ $requestUrl }}</request>

        @if ($error)
            <error code="{{ $error['code'] }}">{{ $error['message'] }}</error>
        @else
            @if ($verb === 'Identify')
                @include('oai-pmh.verbs.identify', ['data' => $data])
            @elseif ($verb === 'ListMetadataFormats')
                @include('oai-pmh.verbs.list-metadata-formats', ['data' => $data])
            @elseif ($verb === 'GetRecord')
                @include('oai-pmh.verbs.get-record', ['data' => $data])
            @elseif ($verb === 'ListRecords')
                @include('oai-pmh.verbs.list-records', ['data' => $data])
            @endif
        @endif
</OAI-PMH>
