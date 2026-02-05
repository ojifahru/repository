<ListMetadataFormats>
    @foreach ($data['formats'] as $format)
        <metadataFormat>
            <metadataPrefix>{{ $format['metadataPrefix'] }}</metadataPrefix>
            <schema>{{ $format['schema'] }}</schema>
            <metadataNamespace>{{ $format['metadataNamespace'] }}</metadataNamespace>
        </metadataFormat>
    @endforeach
</ListMetadataFormats>
