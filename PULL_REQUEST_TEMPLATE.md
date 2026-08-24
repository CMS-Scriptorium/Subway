Title: Fix: strict mode validation and uninitialized prevLastPlace for random selection

This PR makes two small but important fixes to ArrayStep:

1. Mode validation: Only allow a single mode constant (MODE_STILL, MODE_LOOP, MODE_HOLD, MODE_TOGGLE, MODE_RANDOM). Previously combined bitmasks like MODE_LOOP | MODE_TOGGLE were treated as valid; this change makes validation strict and predictable.

2. prevLastPlace initialization: Initialize prevLastPlace to -1 which means "unset" so that RANDOM mode's first selection is not implicitly compared against index 0. Reset also sets prevLastPlace back to -1.

No changes to tests are necessary. The behavior for existing single-mode usages remains unchanged.
