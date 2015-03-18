<?php

namespace Swot\NetworkBundle\Fixtures;


class ThingFixtures {

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
                "id": 12345,
                "classification": "sdf",
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
                                "type": "text",
                                "required": false,
                                "constraints": [
                                    {
                                        "type": "NotBlank",
                                        "message": "Param B may not be blank"
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
                                "required": true
                            },
                            {
                                "name": "Param-2B",
                                "type": "integer",
                                "required": false
                            }
                        ]
                    }
                ],
                "status": [
                    {

                    }
                ]
            }
        }
TAG;

}