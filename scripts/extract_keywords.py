import fitz  # PyMuPDF
import re
import nltk
from nltk.tokenize import sent_tokenize
import json

nltk.download('punkt')

keywords = ["flu", "glucose", "creatinine"]
filename = "pdfs/abd.pdf"
matches = []

doc = fitz.open(filename)

for page_num in range(len(doc)):
    page = doc.load_page(page_num)
    text = page.get_text()

    sentences = sent_tokenize(text)

    for sentence in sentences:
        for keyword in keywords:
            # if re.search(f"\\b{keyword}\\b", sentence, re.IGNORECASE):
            # if re.search(r"\b{}\b".format(keyword), sentence, re.IGlsNORECASE):
            if re.search(rf"\b{keyword}\b", sentence, re.IGNORECASE):
                highlighted = re.sub(rf"(\b{keyword}\b)", r"<mark>\1</mark>", sentence, flags=re.IGNORECASE)
                matches.append({
                    "keyword": keyword,
                    "sentence": highlighted.strip(),
                    "page": page_num + 1,
                    "filename": "abd.pdf"
                })

with open("reports/structured_report.json", "w") as f:
    json.dump(matches, f, indent=2)

print("Extraction complete. JSON report saved to reports/structured_report.json")
