# Relations

One of the primary pieces of functionality provided by Doctrine is to allow us to configure and implement the relationships between our objects.

There is a [small list of the possible kinds of relations](https://www.doctrine-project.org/projects/doctrine-orm/en/2.6/reference/association-mapping.html), though they can be largely grouped into two types:

 * ToMany
 * ToOne
 
 DoctrineStaticMeta also supports the full range of possible relations provided by Doctrine however the approach is opinionated and auto generated.
 
 ## Relation Traits
 
 For each Entity, we generate the full range of possible associations as Traits that can then be used in a related Entity.
 
 You can see the template code in [codeTemplates/src/Entity/Relations/TemplateEntity/Traits](./../codeTemplates/src/Entity/Relations/TemplateEntity/Traits)
 
 As you can see, there are 4 main relation trait folders, these are differentiated by:
 
 * ToMany
 * ToOne
 * Required
 * Not Required
 
 ## Required
 
 A required relation will not be possible to set as null, you must at the time of creation or updating an Entity, provide or maintain a valid Entity or Entities that fulfill the relation.
 
 This can make your code more tricky, however the advantage is that in the getters etc, we no longer have to account for the possiblity of the Entity value being `null`.
 
 Generally, you should use Required relations unless you are really sure that you want it to be possible for there to be no related Entit(y|ies).