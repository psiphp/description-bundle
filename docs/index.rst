Description Bundle
==================

The description bundle integrates the Psi Description component with
Symfony.

See the `Psi Description`_ component documentation for general reference.

Configuration
-------------

By default no description enhancers are enabled or registered. This means that all object
descriptions will be empty.

You will need to register an enhancer with the dependency injection container
as described below and then enable it. If you have registered an enhancer with
the alias ``foobar``, you would enable it in configuration as follows:

.. code-block:: yaml

    psi_description:
        enhancers:
            - foobar

Twig Integration
----------------

The bundle provides a Twig extension which allows you to access descriptions
from within templates:

.. code-block:: jinja

    {% set description = psi_description(my_object) %}

    {% if description.has('std.title') %}
        <h1>{{ description.get('std.title').value }}</h1>
    {% endif %}

Debugging
---------

The bundle provides a ``psi:debug:description`` command which
lists all the available descriptors and allows you to view schema
definitions.

.. code-block:: bash

    $ ./bin/console psi:debug:description --help

Extending
---------

The bundle allows you to easily register new enhancers and schema extensions:

Description Enhancers
~~~~~~~~~~~~~~~~~~~~~

**Tag**: `psi_description.enhancer`

Add a new description enhancer with the alias "foobar":

.. code-block:: xml

    <service id="acme_description.enhancer.foobar" class="Acme\Description\Enhancer\FoobarEnhancer">
        <tag name="psi_description.enhancer" alias="foobar" />
    </service>

Schema Extensions
~~~~~~~~~~~~~~~~~

**Tag**: `psi_description.schema_extension`

Add a new schema_extension with the alias "foobar":

.. code-block:: xml

    _service id="acme_description.schema.extension.foobar" class="Acme\Description\Schema\FoobarExtension">
        <tag name="psi_description.schema_extension" alias="foobar" />
    </service>

.. _Psi Description: https://psiphp.readthedocs.io/en/latest/components/description/docs/index.html
