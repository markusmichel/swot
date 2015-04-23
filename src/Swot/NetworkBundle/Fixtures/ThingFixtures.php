<?php

namespace Swot\NetworkBundle\Fixtures;


class ThingFixtures {

    // success: 200
    // error: 5xx
    // not available: 404
    public static $registerThingResponse = <<<Tag
        {
            "device": {
                "name": "foothing",
                "description": "lfjfdalfjjdflköjalkfdklfkl",
                "profileimage": "http://....",
                "api": {
                    "url": "http://.....",
                },
                "tokens": {
                    "owner_token": "293487239",
                    "write_token": "2305982304",
                    "read_token": "23405834905"
                }
            }
        }
Tag;


    public static $activateFunctionResponse = <<<TAG
    {
		"statusCode":				200,
		"status":					"success",
		"message":					"Temperature was set to 10 degrees",

		"request": {
			"requestedUrl": 		"http://.....",
			"functionName": 		"Name-A",
			"params": [
				{
					"name": 		"Param A",
					"type": 		"text/int/double/email...",
					"required": 	true
				},
				{
					"name": 		"Param B",
					"type": 		"text/int/double/email...",
					"required": 	false
				}
			]
        }
	}
TAG;


    public static $thingResponse = <<<TAG
        {
            "device": {
                "id": 42,
                "name": "foothing",
                "description": "lfjfdalfjjdflköjalkfdklfkl",
                "api": {
                    "url": "http://.....",
                },
                "tokens": {
                    "owner_token": "293487239",
                    "write_token": "2305982304",
                    "read_token": "23405834905"
                },
                "functions": [{
                        "name": "Function-A",
                        "url": "http://www.example.com",
                        "available": true,
                        "parameters": [
                            {
                                "name": "Param-A",
                                "type": "integer",
                                "required": true,
                                "constraints": [
                                    {
                                        "type": "NotNull",
                                        "message": "Param A may not be null"
                                    }
                                ]
                            },
                            {
                                "name": "Param-B",
                                "type": "Choice",
                                "choices": [
                                    1, 2, 3, 4, 5
                                ],
                                "required": false,
                                "constraints": [
                                    {
                                        "type": "NotBlank",
                                        "message": "Param B may not be blank"
                                    }
                                ]
                            },
                            {
                                "name": "Param-C",
                                "type": "Choice",
                                "multiple": true,
                                "expanded": false,
                                "choices": [
                                    1, 2, 3, 4, 5
                                ],
                                "required": false,
                                "constraints": [
                                    {
                                        "type": "NotBlank",
                                        "message": "Param C may not be blank"
                                    }
                                ]
                            },
                            {
                                "name": "Param-D",
                                "type": "Choice",
                                "multiple": true,
                                "expanded": true,
                                "choices": [
                                    1, 2, 3, 4, 5
                                ],
                                "required": false,
                                "constraints": [
                                    {
                                        "type": "NotBlank",
                                        "message": "Param D may not be blank"
                                    }
                                ]
                            }
                        ]}, {
                        "name": "Function-B",
                        "url": "http://www.example.com",
                        "available": true,
                        "parameters": [
                            {
                                "name": "Param-2A",
                                "type": "text",
                                "required": true,
                                "constraints": [
                                    {
                                        "type": "Locale",
                                        "message": "Param 2A must be a locale"
                                    }
                                ],
                                "defaultValue": "hallo"
                            },
                            {
                                "name": "Param-2B",
                                "type": "integer",
                                "required": false,
                                "readOnly": true,
                                "defaultValue": 20
                            }
                        ]
                    }
                ],
                "information": [
                    {

                    }
                ]
            }
        }
TAG;

    public static $informationResponse = <<<TAG
    {
		"information":
		[
            {
                "title" : "Status",
                "value" : "working"
            },

            {
                "title": "Working",
                "type": "boolean",
                "value": true
            },

            {
                "title" : "value",
                "type" : "table",
                "value" : {
                    "header" : ["header1", "header2", "header3"],
                    "data" : [
                        ["value1", "value2", "value3"],
                        ["value1", "value2", "value3"]
                    ]
                }
            },

            {
                "title" : "Progress making coffee",
                "type" : "percentage",
                "value" : "0"
            },

            {
                "title" : "value",
                "type" : "html",
                "value" : "<div><b>html value example</b></div>"
            },

            {
                "title": "finished",
                "type": "boolean",
                "value": false
            }
        ]
	}
TAG;

}