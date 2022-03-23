# CORE - Use Attributes for Entities and store Attributes in storage even if table of entity has it's own column

Date: 2022-03-10

## Status

Accepted

## Context

Creating new entities using ContentType needs to store somethere values from fields.
ContentType and Attributes can be mixed together. Every Field from ContentType can be stored as Attribute in Entity.

## Decision

We combine Attributes with ContentTypes for Fields. Attributes is a supportive Bounded Context, and ContentType
is a Root Bounded Context. Every Field will be mapped to Attribute (by value and URI).

Storage of the Attributes requires to store all attributes, even if any of the Fields has it's own column
in the Entity table. That's important due to show the edit form of the Entity of any Content Type,
and also simplify the code.

## Consequences

- Simplify the code of managing Entity in form
- If we want to store some attribute value in separate column in separate table, the value will be duplicated.
  - For that we need to this of this as a View column. Source of true is a Attribute.
