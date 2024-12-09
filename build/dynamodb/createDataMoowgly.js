//////////////////////////////////////////////////////////////////////////
// Write a Single Guest
//////////////////////////////////////////////////////////////////////////

// 1
var params = {
    TableName: "Guest",
    Item: {
        "id_guest":"f259bb65-e0bb-4e87-afa4-5d7c132c066e",
        "email":"gwendaldugue@gmail.com",
        "family_name":"Dugué",
        "first_name":"Gwendal",
        "telephone": "06 69 02 46 65",
        "address": {
            "street":"55 boulevard Sérurier",
            "zip_code":"75019",
            "city":"Paris",
            "country":"France"
        },
        "avg_rating" : 5,
        "avg_kids_rating" : 4.5,
        "geo_location" : { "lon" : 2.287592000000018, "lat" : 48.8782947 },
        "kids" : [  {"kid_name" : "Yoann", "age" : 20 , "activity" : [{ "sport" : ["football"] }, {"game" : ["chess"]}], "activity_code" : ["ftb", "che"], "rating" : 5 },
                    {"kid_name" : "Marie", "age" : 15 , "activity" : [{ "art" : ["danse"] }, { "game" : ["chess"]}],"activity_code" : ["dan", "che"], "rating" : 4}
        ]

    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 2
var params = {
    TableName: "Guest",
    Item: {
        "id_guest":"9771c63b-f306-4220-85d6-c3c69e82b886",
        "email":"emiliemoysson@gmail.com",
        "family_name":"Moysson",
        "first_name":"Emilie",
        "telephone": "06 22 60 99 99",
        "address": {
            "street":"14 rue Condorcet",
            "zip_code":"75009",
            "city":"Paris",
            "country":"France"
        },
        "geo_location" : { "lon" : 2.3477612000000363, "lat" : 48.8796933 },
        "kids" : [  {"kid_name" : "Jean", "age" : 14 , "activity" : [{ "art" : ["painting"] }], "activity_code": ["pai"]}
        ]

    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 3
var params = {
    TableName: "Guest",
    Item: {
        "id_guest":"ac50a410-b68b-4edc-97b0-a39529e97e5a",
        "email":"anaxnake@gmail.com",
        "family_name":"Gaidos",
        "first_name":"Christos",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Grunwaldzka 25",
            "city":"Poznań",
            "region":"Wielkopolska",
            "country":"Polska"
        },
        "geo_location" : { "lon" : 16.896784900000057, "lat" : 52.404722 },
        "kids" : [  {"kid_name" : "Aleksy", "age" : 17 , "activity" : [{ "culture" : ["architecture"] }], "activity_code": ["ate"]},
                    {"kid_name" : "Cyryl", "age" : 17 , "activity" : [{ "culture" : ["architecture"] }], "activity_code": ["ate"]},
        ]

    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 4
var params = {
    TableName: "Guest",
    Item: {
        "id_guest":"89d240c9-0d38-4441-b5df-001886c47c95",
        "email":"gwendal@eptamel.com",
        "family_name":"Dupond",
        "first_name":"Pierre"

    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 5
var params = {
    TableName: "Guest",
    Item: {
        "id_guest":"76890f93-9405-4676-94f5-adf2a0b2f831",
        "email":"cgaidos@eptamel.com",
        "family_name":"Gaidosky",
        "first_name":"Christosian",
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Write a Single Host
//////////////////////////////////////////////////////////////////////////

//1

var params = {
    TableName: "Host",
    Item: {
        "id_host":"ac50a410-b68b-4edc-97b0-a39529e97e5a",
        "email":"anaxnake@gmail.com",
        "family_name":"Gaidos",
        "first_name":"Christos",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Grunwaldzka 25",
            "city":"Poznań",
            "region":"Wielkopolska",
            "country":"Polska"
        },
        "geo_location" : { "lon" : 16.896784900000057, "lat" : 52.404722 },
        "type_of_home" : "apartment",
        "parents": [{"profession" : "webmaster", "gender" : "male"}],
        "kids" : [{"age" : "17", "gender":"male"}, {"age" : 17, "gender" : "male"}],
        "description" : [{ "en" : "We are a family bla bla" }, {"fr" : "Nous sommes une famille bla bla"}],
        "activity": [{"game" : ["chess"]}],
        "activity_code" : ["che"],
        "nb_welcomed_kids" : 2,
        "age_min" : 15,
        "age_max" : 25,
        "languages" : ["english", "french", "polish"],
        "welcomed_adult" : true,
        "calendar_a" : [{ "from": "2017-03-01", "to" : "2017-06-30" },{ "from": "2017-10-06" , "to" : "2017-10-08" } ],
        "calendar_b" : [{ "from": "2017-07-01" , "to" : "2017-09-30" }],
        "calendar_u" :[{ "from": "2017-10-01" , "to" : "2017-10-05" },{ "from": "2017-10-09" , "to" : "2017-03-01" }],
        "daily_price" : 30,
        "currency" : "EUR",
        "avg_rating" : 4.5,
        "charter" : true
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//2
var params = {
    TableName: "Host",
    Item: {
        "id_host":"89d240c9-0d38-4441-b5df-001886c47c95",
        "email":"gwendal@eptamel.com",
        "family_name":"Dupond",
        "first_name":"Pierre",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Via Antonio Giuriolo, 6",
            "city":"Padova",
            "zip_code":"35129",
            "region":"Veneto",
            "country":"Italia"
        },
        "geo_location" : { "lon" : 11.912131999999929, "lat" : 45.42524119999999 },
        "type_of_home" : "apartment",
        "parents": [{"profession" : "webmaster", "gender" : "male"}, {"profession" : "panettiere", "gender" : "female"}],
        "kids" : [{"age" : "17", "gender":"male"}, {"age" : 17, "gender" : "male"}],
        "description" : [{ "en" : "We are a family bla bla" }, {"fr" : "Nous sommes une famille bla bla"}],
        "activity": [{"art" : ["painting"]}],
        "activity_code" : ["pai"],
        "nb_welcomed_kids" : 2,
        "age_min" : 15,
        "age_max" : 25,
        "languages" : ["english", "french", "italian"],
        "welcomed_adult" : true,
        "calendar_a" : [{ "from": "2017-03-01", "to" : "2017-06-30" },{ "from": "2017-10-06" , "to" : "2017-10-08" } ],
        "calendar_b" : [{ "from": "2017-07-01" , "to" : "2017-09-30" }],
        "calendar_u" :[{ "from": "2017-10-01" , "to" : "2017-10-05" },{ "from": "2017-10-09" , "to" : "2017-03-01" }],
        "daily_price" : 30,
        "currency" : "EUR",
        "charter" : true
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//3
var params = {
    TableName: "Host",
    Item: {
        "id_host":"76890f93-9405-4676-94f5-adf2a0b2f831",
        "email":"cgaidos@eptamel.com",
        "family_name":"Gaidosky",
        "first_name":"Christosian",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Via Plauna 39",
            "city":"Laax",
            "zip_code":"7031",
            "country":"Suisse"
        },
        "geo_location" : { "lon" : 2.287592000000018, "lat" : 46.8120529 },
        "type_of_home" : "house",
        "parents": [{"profession" : "webmaster", "gender" : "male"}, {"profession" : "panettiere", "gender" : "female"}],
        "kids" : [{"age" : "17", "gender":"male"}, {"age" : 17, "gender" : "male"}],
        "description" : [{ "en" : "We are a family bla bla" }, {"fr" : "Nous sommes une famille bla bla"}],
        "activity": [{"sport" : ["ski"]}],
        "activity_code" : ["ski"],
        "nb_welcomed_kids" : 2,
        "age_min" : 15,
        "age_max" : 25,
        "languages" : ["english", "french", "italian"],
        "welcomed_adult" : true,
        "calendar_a" : [{ "from": "2017-03-01", "to" : "2017-06-30" },{ "from": "2017-10-06" , "to" : "2017-10-08" } ],
        "calendar_b" : [{ "from": "2017-07-01" , "to" : "2017-09-30" }],
        "calendar_u" :[{ "from": "2017-10-01" , "to" : "2017-10-05" },{ "from": "2017-10-09" , "to" : "2017-03-01" }],
        "daily_price" : 30,
        "currency" : "EUR",
        "charter" : true
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

//////////////////////////////////////////////////////////////////////////
// Write a Single Offer
//////////////////////////////////////////////////////////////////////////

// 1
var params = {
    TableName: "Offer",
    Item: {
        "id_offer":"1",
        "id_host": "76890f93-9405-4676-94f5-adf2a0b2f831",
        "email":"cgaidos@eptamel.com",
        "family_name":"Gaidosky",
        "first_name":"Christosian",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Via Plauna 39",
            "city":"Laax",
            "zip_code":"7031",
            "country":"Suisse"
        },
        "geo_location" : { "lon" : 2.287592000000018, "lat" : 46.8120529 },
        "nb_seats" : 4,
        "fill_rate" : 4,
        "title" : "Ski holidays",
        "description" : [{ "en" : "Ski holidays for 4 people, 600€ by person" }, {"fr" : "Vacances au ski pour 4 personnes, 600€ par personne"}],
        "activity": [{"sport" : ["ski"]}],
        "activity_code" : ["ski"],
        "date_range" : [ {"from" : "2017-04-01"}, {"to" :"2017-04-07"} ],
        "type" : "activity",
        "meal" : "include",
        "accomodation" : "include",
        "price" : 600,
        "currency" : "EUR"
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 2
var params = {
    TableName: "Offer",
    Item: {
        "id_offer":"2",
        "id_host":"ac50a410-b68b-4edc-97b0-a39529e97e5a",
        "email":"anaxnake@gmail.com",
        "family_name":"Gaidos",
        "first_name":"Christos",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Grunwaldzka 25",
            "city":"Poznań",
            "region":"Wielkopolska",
            "country":"Polska"
        },
        "geo_location" : { "lon" : 16.896784900000057, "lat" : 52.404722 },
        "nb_seats" : 12,
        "fill_rate" : 3,
        "title" : "Learn to play chess with a champion",
        "description" : [{ "en" : "From 2 pm to 6 pm, we can learn !" }, {"fr" : "De 14h à 18h, nous pouvons apprendre "}],
        "activity": [{"Game" : ["Chess"]}],
        "activity_code": ["che"],
        "date_range" : [ {"from" : "2017-04-02"}, {"to" :"2017-05-28"} ],
        "type" : "activity",
        "meal" : "exclude",
        "accomodation" : "exclude",
        "price" : 10,
        "currency" : "EUR"
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 3
var params = {
    TableName: "Offer",
    Item: {
        "id_offer":"3",
        "id_host":"89d240c9-0d38-4441-b5df-001886c47c95",
        "email":"gwendal@eptamel.com",
        "family_name":"Dupond",
        "first_name":"Pierre",
        "telephone": "06 01 02 03 04",
        "address": {
            "street":"Via Antonio Giuriolo, 6",
            "city":"Padova",
            "zip_code":"35129",
            "region":"Veneto",
            "country":"Italia"
        },
        "geo_location" : { "lon" : 11.912131999999929, "lat" : 45.42524119999999 },
        "nb_seats" : 6,
        "title" : "Learn to paint like Van Gogh",
        "description" : [{ "en" : "Learn to paint like Van Gogh in 2 day !!" }, {"fr" : "Apprenez à peindre comme Van Gogh en 2 jours"}],
        "activity": [{"Art" : ["Painting"]}],
        "activity_code": ["pai"],
        "date_range" : [ {"from" : "2017-04-24"}, {"to" :"2017-04-26"} ],
        "type" : "activity",
        "meal" : "exclude",
        "accomodation" : "exclude",
        "price" : 100,
        "currency" : "EUR"
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});


//////////////////////////////////////////////////////////////////////////
// Write a Single Message
//////////////////////////////////////////////////////////////////////////

// 1
var params = {
    TableName: "Message",
    Item: {
        "id_guest":"89d240c9-0d38-4441-b5df-001886c47c95",
        "id_host":"ac50a410-b68b-4edc-97b0-a39529e97e5a",
        "host_family_name" : "Gaidos",
        "host_first_name" : "Christos",
        "guest_family_name" : "Dupond",
        "guest_first_name" : "Pierre",
        
        "body" : [{"sender" : "G", "msg" : "Hello !", "date" : "2017-03-16T17:00:00Z", "notif" : true }, 
                  {"sender" : "H", "msg" : "How are you ?", "date" : "2017-03-16T18:00:00Z", "notif" : true},
                  {"sender" : "G", "msg" : "Fine and you ?", "date" : "2017-03-16T18:05:00Z", "notif" : false}]
        
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 2
var params = {
    TableName: "Message",
    Item: {
        "id_guest":"89d240c9-0d38-4441-b5df-001886c47c95",
        "id_host":"76890f93-9405-4676-94f5-adf2a0b2f831",
        "host_family_name" : "Gaidosky",
        "host_first_name" : "Christosian",
        "guest_family_name" : "Dupond",
        "guest_first_name" : "Pierre",
        
        "body" : [{"sender" : "G", "msg" : "Hello Chrsitian!", "date" : "2017-03-16T17:00:00Z", "notif" : true }, 
                  {"sender" : "H", "msg" : "How are you Pierre?", "date" : "2017-03-16T18:00:00Z", "notif" : true},
                  {"sender" : "G", "msg" : "Fine and you ?", "date" : "2017-03-16T18:05:00Z", "notif" : false}]
        
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});

// 3
var params = {
    TableName: "Message",
    Item: {
        "id_guest":"1",
        "id_host":"2",
        "host_family_name" : "Gégé",
        "host_first_name" : "Rar",
        "guest_family_name" : "DD",
        "guest_first_name" : "Michou",
        
        "body" : [{"sender" : "G", "msg" : "Hello Chrsitian!", "date" : "2017-03-16T17:00:00Z", "notif" : true }, 
                  {"sender" : "H", "msg" : "How are you Pierre?", "date" : "2017-03-16T18:00:00Z", "notif" : true},
                  {"sender" : "G", "msg" : "Fine and you ?", "date" : "2017-03-16T18:05:00Z", "notif" : false}]
        
    }
};
docClient.put(params, function(err, data) {
    if (err)
        console.log(JSON.stringify(err, null, 2));
    else
        console.log(JSON.stringify(data, null, 2));
});