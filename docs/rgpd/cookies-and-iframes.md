# Cookies and Iframes

The media category covers third-party iframe services.
The current demo uses YouTube through IframeManager.

## Rules

- no iframe should load before media consent exists
- the placeholder must remain visible without breaking the layout
- the consent modal remains the single place where users can review or revoke the choice

This keeps the embed policy explicit and prevents privacy logic from spreading across random Vue components.
