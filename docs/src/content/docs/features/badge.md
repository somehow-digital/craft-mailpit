---
title: Unread badge
description: See the number of unread messages at a glance.
---

The navigation item or the utility shows a badge with the number of unread messages in Mailpit.

The plugin reads the number from the `/api/v1/info` endpoint of the configured [host](/guide/settings/). If Mailpit can't be reached or returns something unexpected, no badge is shown.

:::note
The badge is requested by the Craft server, so the host must be reachable from there, not only from your browser.
:::
