const { GoogleGenerativeAI } = require("@google/generative-ai");
const express = require('express');
const router = express.Router();

const genAI = new GoogleGenerativeAI(process.env.GEMINI_API_KEY);

router.post('/ask-axel', async (req, res) => {
  try {
    // adatok, jegyzet
    const { message, subject, topic, notes } = req.body;
    
    const model = genAI.getGenerativeModel({ model: "gemini-1.5-flash" });

    const sourceMaterial = notes 
      ? notes 
      : "Nincs megadva konkrét tananyag, használd az általános tudásodat.";

    // Alap prompt
    const systemPrompt = `
      SZEREP:
      Te Axel vagy, a MaturaSmart intelligens, fiatalos és türelmes érettségi felkészítő tutorja. 🤖🎓
      
      KONTEXTUS:
      Tantárgy: ${subject || 'Általános'}
      Témakör: ${topic || 'Általános'}
      
      ### 📖 FORRÁSANYAG (JEGYZET):
      Az alábbi tananyagra / tananyag alapján válaszolj! Ha a válasz megtalálható ebben a szövegben, akkor ezt használd elsődleges forrásként, és egészítsd ki a saját tudásoddal.
      
      """
      ${sourceMaterial}
      """
      
      INSTRUKCIÓK:
      1. Stílus: Tegeződj, légy közvetlen, használj emojikat.
      2. Pedagógia: Ne csak a megoldást mondd meg! Magyarázd el úgy, mintha a fenti jegyzetet értelmeznéd a diáknak.
      3. Matek esetén: Vezesd le lépésről lépésre.
      4. Nyelvtan esetén: Adj példákat a szabályokra.
      5. Történelem esetén: Helyezd el a kontextusban az eseményeket.
      5. Ha a kérdés nem kapcsolódik a fenti tantárgyhoz vagy témakörhöz, udvariasan jelezd, hogy ebben nem tudsz segíteni.
      4. Formázás: Használj Markdown-t (félkövér, listák, stb.) a válaszodban.
      
      A diák kérdése:
    `;

    const result = await model.generateContent(`${systemPrompt} ${message}`);
    const response = await result.response;
    const text = response.text();

    res.json({ answer: text });

  } catch (error) {
    console.error("AI Hiba:", error);
    res.status(500).json({ error: "Axel most pihen... (Hiba történt)" });
  }
});

module.exports = router;