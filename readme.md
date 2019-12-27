# IronBound State
IronBound State is a State Machine library heavily influenced by `yohang/finite`.

## Core Components
### Subject
The subject is the object that maintains state and has transitions applied to it. The only requirement is that it is a PHP object; there is no `interface` defined for subjects. 

### State
The `State` object represents the current state the subject is in. For instance, a post could be in the "Pending" state or "Published" state. A delivery state could be "Processing", "Waiting at Carrier", "In Transit", and "Delivered".

### Transition
The `Transition` object defines how a subject moves from one state to another. For instance, when you "Publish" a post it moves from the "Pending" state to the "Published" state. When you "Deliver to Carrier" a package, it transitions from "Processing" to "Waiting at Carrier".

### Graph
The `Graph` object is responsible for holding the set of available states a subject can be in and the list of transitions between those states. 

A subject can have more than one Graph. For instance, an ecommerce order might have a payment status and a delivery status. Each status is a separate graph.

### State Machine
The `StateMachine` is how you interact with a subject's state and transition between states. It can tell you what transitions are available from the current state and apply a transition to change the subject's state.

### State Mediator
There are many different ways that a subject can store and change it's state. For instance, it could be an instance property, behind a method, or perhaps tracked somewhere completely separate from the subject's data. The `StateMediator` is used to abstract these details away from the `StateMachine`. 

## Factories
The most direct way to use IronBound State is to instantiate a `ConcreteStateMachine` directly.

```php
use IronBound\State\Graph\{GraphId, MutableGraph};
use IronBound\State\ConcreteStateMachine;
use IronBound\State\StateMediator\PropertyStateMediator;
use IronBound\State\State\{StateId, MutableState, StateType};
use IronBound\State\Transition\{TransitionId, MutableTransition};

$graph = new MutableGraph(new GraphId('status'));
$graph->addState(new MutableState(new StateId('pending'), StateType::INITIAL()));
$graph->addState(new MutableState(new StateId('published')));
$graph->addTransition(new MutableTransition(
    new TransitionId('publish'),
    [ new StateId('pending') ],
    new StateId('published')
));

$mediator = new PropertyStateMediator('status');

$stateMachine = new ConcreteStateMachine($mediator, $graph->toImmutable(), $subject);
```

However, you may prefer an alternate construction style where you use a Factory that is preconfigured.

```php
use IronBound\State\Graph\GraphId;
use IronBound\State\Factory\StateMachineFactory;

/** @var StateMachineFactory $factory*/
$factory->make($subject, new GraphId('status'));
``` 

The recommended way to do this is to use the `StateMachineFactoryConfigurator` class to do the heavy lifting based on a configuration array.

```php
use IronBound\State\Graph\GraphId;
use IronBound\State\State\StateType;
use IronBound\State\Factory\StateMachineFactoryConfigurator;

$config = [
    [
        // The test determines which Graphs apply to the given subject. This means that
        // GraphIds only have to be unique to the subject type instead of globally.
        'test'   => [
            // This particular test type checks if the subject is an instance of the given class. 
            'class' => 'Order',
        ],
        // The list of all the graphs for this subject.
        'graphs' => [
            'payment'  => [
                'mediator'    => [
                    // Use a mediator that checks against an object's properties.
                    'property' => 'paymentStatus',
                ],
                // The list of all the available states
                'states'      => [
                    'unpaid'   => [
                        // Specifies the StateType manually. The default is NORMAL.
                        'type' => StateType::INITIAL,
                    ],
                    // If you don't need any extra configuration options,
                    // you can just specify a string with no key.
                    'processing',
                    'paid',
                    'refunded' => [
                        'type' => StateType::FINAL,
                    ],
                ],
                // The list of all the available transitions
                'transitions' => [
                    'pay'      => [
                        // The list of states this transition can be applied from
                        'from' => 'unpaid',
                        // The state the subject will be in after transitioning. 
                        'to'   => 'processing',
                    ],
                    'complete' => [
                        'from' => 'processing',
                        'to'   => 'paid',
                    ],
                    'refund'   => [
                        'from' => [ 'processing', 'paid' ],
                        'to'   => 'refunded',
                    ],
                ],
            ],
            'delivery' => [
                'mediator'    => [
                    'property' => 'deliveryStatus',
                ],
                'states'      => [
                    'processing' => [
                        'type' => StateType::INITIAL,
                    ],
                    'in-transit',
                    'delivered'  => [
                        'type' => StateType::FINAL,
                    ]
                ],
                'transitions' => [
                    'drop-at-carrier' => [
                        'from' => 'processing',
                        'to'   => 'in-transit',
                    ],
                    'deliver'         => [
                        'from' => 'in-transit',
                        'to'   => 'delivered',
                    ],
                ],
            ],
        ],
    ],
    [
        'test'   => [
            'class' => 'BlogPost',
        ],
        'graphs' => [
            'states'      => [
                'draft'     => [
                    'type' => StateType::INITIAL,
                ],
                'published' => [
                    'type' => StateType::FINAL,
                ],
            ],
            'transitions' => [
                'publish' => [
                    'from' => 'draft',
                    'to'   => 'published',
                ],
            ],
        ],
    ],
];

$stateMachineFactory = (new StateMachineFactoryConfigurator())->configure( $config );

$paymentStateMachine = $stateMachineFactory->make(new Order(), new GraphId('payment'));
$deliveryStateMachine = $stateMachineFactory->make(new Order(), new GraphId('delivery'));
$blogPostStateMachine = $stateMachineFactory->make(new BlogPost(), new GraphId('status'));
```
