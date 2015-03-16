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
                        "name": "Function A",
                        "url": "www.....",
                        "available": true,
                        "params": [
                            {
                                "name": "Param A",
                                "type": "text/int/double/email...",
                                "required": true
                            },
                            {
                                "name": "Param B",
                                "type": "text/int/double/email...",
                                "required": false
                            }
                        ]}, {
                        "name": "Function B",
                        "url": "www.....",
                        "available": true,
                        "params": [
                            {
                                "name": "Param A",
                                "type": "text/int/double/email...",
                                "required": true
                            },
                            {
                                "name": "Param B",
                                "type": "text/int/double/email...",
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