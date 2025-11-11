# SEO Validation Checklist - Saffron The Indian Kitchen

## 1. Structured Data
- [ ] Validate index.html at https://validator.schema.org
  - Expect: Restaurant + LocalBusiness graph, no errors or warnings.
- [ ] Validate contact.html at https://validator.schema.org
  - Expect: ContactPage referencing #restaurant, no errors.

## 2. Meta & Social Previews
- [ ] Open Graph preview: https://www.opengraph.xyz
  - Test both homepage and contact page URLs.
  - Confirm title/description match new copy and hero image loads.
- [ ] Twitter Card validator: https://cards-dev.twitter.com/validator
  - Test both URLs.
  - Confirm summary_large_image renders correctly.
- [ ] Meta description length
  - Use https://www.character-counter.org or similar.
  - Ensure homepage/contact descriptions remain within 150-160 characters.

## 3. Mobile Usability
- [ ] Google Mobile-Friendly Test: https://search.google.com/test/mobile-friendly
  - Run for homepage and contact page.
  - Expect “Page is mobile friendly.”

## 4. Rich Results Eligibility
- [ ] Google Rich Results Test: https://search.google.com/test/rich-results
  - Run for homepage.
  - Expect valid Restaurant rich results (no critical errors).

## 5. Post-Launch Tasks
- [ ] Resubmit sitemap.xml in Google Search Console and Bing Webmaster Tools.
- [ ] Monitor Google Search Console coverage report for new warnings.
- [ ] Capture before/after Lighthouse SEO scores for reporting.
