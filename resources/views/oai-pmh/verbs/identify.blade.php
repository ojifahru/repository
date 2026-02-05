<Identify>
    <repositoryName>{{ $data['repositoryName'] }}</repositoryName>
    <baseURL>{{ $data['baseURL'] }}</baseURL>
    <protocolVersion>{{ $data['protocolVersion'] }}</protocolVersion>
    <adminEmail>{{ $data['adminEmail'] }}</adminEmail>
    <earliestDatestamp>{{ $data['earliestDatestamp'] }}</earliestDatestamp>
    <deletedRecord>{{ $data['deletedRecord'] }}</deletedRecord>
    <granularity>{{ $data['granularity'] }}</granularity>
    <description>
        <oai-identifier xmlns="http://www.openarchives.org/OAI/2.0/oai-identifier"
            xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance"
            xsi:schemaLocation="http://www.openarchives.org/OAI/2.0/oai-identifier http://www.openarchives.org/OAI/2.0/oai-identifier.xsd">
            <scheme>oai</scheme>
            <repositoryIdentifier>{{ $data['repositoryIdentifier'] }}</repositoryIdentifier>
            <delimiter>:</delimiter>
            <sampleIdentifier>oai:{{ $data['repositoryIdentifier'] }}:{{ $data['resourceType'] }}/1</sampleIdentifier>
        </oai-identifier>
    </description>
</Identify>
