# Cookies and Iframes

The media category covers third-party iframe services.
The current demo uses YouTube through IframeManager.

## Rules

- no iframe should load before media consent exists
- the placeholder must remain visible without breaking the layout
- the consent modal remains the single place where users can review or revoke the choice
- the footer privacy entry point should remain available so consent can be revisited after the first decision
- iframe notices and action labels should match the current public locale

This keeps the embed policy explicit and prevents privacy logic from spreading across random Vue components.
