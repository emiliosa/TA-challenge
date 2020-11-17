# Inventory resource

## GET /inventory<br>
* Get how many Death Stars are in the inventory of starships:
``` 
curl --request GET '/inventory?unit_type=starship?tags=death_stars'
{
  "description": "How many Death Stars are in the inventory of starships",
  "payload": {},
  "count": 5
}
```
* Get how many vehicles has 3 or more passangers:
```
curl --request GET '/inventory?unit_type=vehicle?tags=passangers:3'
{
  "description": "How many vehicles has 3 or more passangers",
  "payload": {},
  "count": 10
}
```

## PATCH /inventory
* Set the number Death Stars in the inventory of starships:
```
curl --request PATCH '/inventory?unit_type=vehicle&tags=death_stars' \
--header 'Content-Type: application/x-www-form-urlencoded' \
--data-urlencode 'count=15'
{
  "description": "How many Death Stars are in the inventory of starships",
  "payload": {},
  "count": 15
}
```

## POST /inventory/increment
* Increment the total number of units for a specific starship or vehicle:
```
curl --request POST '/inventory/increment?unit_type=vehicle'
{
  "description": "Total number of units",
  "payload": {},
  "count": 14
}
```

## POST /inventory/decrement
* Decrement the total number of units for a specific starship or vehicle:
```
curl --request POST '/inventory/decrement?unit_type=vehicle'
{
  "description": "Total number of units",
  "payload": {},
  "count": 16
}
```

## One way to achieve this is to use some predefined tags, like:<br>
- tags={'name': 'death_stars'} (where **death_stars** relates to starships/vehicles attribute's value, eg: 'Death Stars')<br>
- tags={'passangers': 3} (where **passangers** relates to vehicles with GTE (greater than or equal) passangers attribute's value)<br>

# Questions
* <i>Allow to get the total number of units for a specific starship or vehicle. Example: get how many Death Stars are in the inventory of starships</i><br>
If "Death Stars" relates to starship.name of vehicle.name attribute, service would query /starships or /vehicles and filter by this "Death Stars" name.
After this i would save count value in inventory model.
* <i>Allow to set the total number of units for a specific starship or vehicle. Example: set the number Death Stars in the inventory of starships</i><br>
This count value would not match with provided by querying /starships or /vehicles services, so maybe this would not be consistent.
Am i in the rigth path or did i misunderstund it?


