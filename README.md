# Supplier Database (Leverantördatabas)

A web‑based database developed for the Swedish museum railways (MRO) to share experiences with external suppliers.  
The system helps member organizations identify suitable suppliers, exchange risk assessments, and publish structured articles with documentation.

Originally requested at the MRO Autumn Meeting 2024 and presented at the Spring Meeting 2025.

---

## 📌 Purpose

The Supplier Database enables:
- Sharing of supplier experiences between museum railways  
- Uploading and distributing risk assessments  
- Publishing articles with images and documents  
- Searching and filtering among all published material  

All participating organizations share the responsibility of keeping the database up to date.

## 🧭 Navigation

The left‑side navigation menu provides access to the system’s pages. Available options depend on user permissions.

| Page | Description |
|------|-------------|
| **Home** | Version history and general information |
| **Articles** | Browse and filter published articles |
| **Create Article** | Create a new article (author permission required) |
| **My Articles** | Manage articles authored by the logged‑in user |

---

## 📄 Reading Articles

The **Articles** page displays the 20 most recently published articles.  
To refine the results, use the filter panel:

- Filters can be expanded/collapsed
- Text fields match *contains*, not exact match
- Unused fields do not restrict results
- Use **Filter** to apply and **Clear filters** to reset

Opening an article displays:
- Full article text  
- Author and organization  
- Images (click to open in full size)  
- Attached documents (downloadable)

A **Back** button returns to the filtered list.

---

## ✍️ Creating Articles (Author Permission Required)

Authors can create new articles via **Create Article**.

### Required fields
Fields marked with a red asterisk (*) must be filled in before submission.

### Attachments
- **Risk assessment** (max 1, always displayed first)
- Additional files: **PDF, DOCX, DOC, ODT, JPG, JPEG, PNG, GIF**

> Only upload files you have the rights to use.

After submission:
- A green confirmation box appears
- The article enters the state **“Not yet reviewed by moderator”**

---

## 🗂 My Articles

Authors can view, filter, edit, and delete their own articles.

### Status indicators
Each article has one of the following statuses:

- **Submitted, not yet reviewed**
- **Denied** (with explanation)
- **Approved**
- **Deleted**

Deleted articles can be restored by the author.

### Editing
All fields from article creation are available.  
Attachments can be added or removed:

- Each existing attachment has a trash‑icon box  
- Clicking it marks the attachment for deletion  
- Saving the article resubmits it for moderation  
- No version history is stored — changes cannot be undone

---

## 🛠 Development & Future Improvements
### Planned features (as of 2025‑02‑26)

| Feature | Size | Priority | Status |
|--------|------|----------|--------|
| Pagination for Articles & My Articles | Medium | High | Not started |
| Additional relevant categories | Small | High | In progress |
| Updated UI for functions | Medium | Low | Not started |
| Mobile‑friendly interface | Large | Low | Not started |
| Free naming of uploaded documents | Medium | Medium | Not started |
| Status logic for users & organizations | Medium | High | Not started |
| Article comments | Large | Medium | Not started |
| Email notifications | Medium | Medium | Not started |
| Rating / review fields | Large | High | Not started |
| Automatic URL correction | Medium | High | Not started |
| Confirmation dialogs | Large | Medium | Not started |
