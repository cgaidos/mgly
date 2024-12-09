//////////////////////////////////////////////////////////////////////////
// Create Table Guest
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Guest",
    KeySchema: [
        { AttributeName: "id_guest", KeyType: "HASH" },
    ],
    AttributeDefinitions: [
        { AttributeName: "id_guest", AttributeType: "S" },
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Create Table Host
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Host",
    KeySchema: [
        { AttributeName: "id_host", KeyType: "HASH" },
    ],
    AttributeDefinitions: [
        { AttributeName: "id_host", AttributeType: "S" },
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Create Table Offer
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Offer",
    KeySchema: [
        { AttributeName: "id_offer", KeyType: "HASH" },
    ],
    AttributeDefinitions: [
        { AttributeName: "id_offer", AttributeType: "S" }
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Create Table Message
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Message",
    KeySchema: [
        { AttributeName: "id_guest", KeyType: "HASH" },
        { AttributeName: "id_host", KeyType: "RANGE" }
    ],
    AttributeDefinitions: [
        { AttributeName: "id_guest", AttributeType: "S" },
        { AttributeName: "id_host", AttributeType: "S" }
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Create Table Review
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Review",
    KeySchema: [
        { AttributeName: "id_guest", KeyType: "HASH" },
        { AttributeName: "id_host", KeyType: "RANGE" }
    ],
    AttributeDefinitions: [
        { AttributeName: "id_guest", AttributeType: "S" },
        { AttributeName: "id_host", AttributeType: "S" }
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Create Table Booking
//////////////////////////////////////////////////////////////////////////

var params = {
    TableName : "Booking",
    KeySchema: [
        { AttributeName: "id_guest", KeyType: "HASH" },
        { AttributeName: "id_host", KeyType: "RANGE" }
    ],
    AttributeDefinitions: [
        { AttributeName: "id_guest", AttributeType: "S" },
        { AttributeName: "id_host", AttributeType: "S" }
    ],
    ProvisionedThroughput: {
        ReadCapacityUnits: 5,
        WriteCapacityUnits: 5
    }
};

dynamodb.createTable(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});
