// filename: worker.js (ถ้าผ่าน Dashboard ก็วางใน Editor ได้เลย)
export default {
  async fetch(request, env) {
    const corsHeaders = {
      "Access-Control-Allow-Origin": "*",
      "Access-Control-Allow-Methods": "GET,POST,OPTIONS",
      "Access-Control-Allow-Headers": "Content-Type, Authorization",
    };
    if (request.method === "OPTIONS") {
      return new Response(null, { headers: corsHeaders });
    }

    try {
      const url = new URL(request.url);
      if (url.pathname !== "/chat") {
        return new Response("OK", { headers: corsHeaders });
      }

      const bodyText = await request.text();
      const params = new URLSearchParams(bodyText);
      const msg = params.get("msg") || url.searchParams.get("msg") || "";
      if (!msg) return new Response("ไม่มีข้อความ", { headers: corsHeaders });

      const payload = {
        model: "meta-llama/llama-3.1-8b-instruct",
        messages: [
          { role: "system", content: "ตอบเป็นภาษาไทย น้ำเสียงเป็นกันเอง สุภาพ น่ารัก และกระชับ" },
          { role: "user", content: msg }
        ],
        temperature: 0.7
      };

      const res = await fetch("https://openrouter.ai/api/v1/chat/completions", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "Authorization": `Bearer ${env.OPENROUTER_API_KEY}`,
          "HTTP-Referer": url.origin,
          "X-Title": "Pink LLaMA Chat"
        },
        body: JSON.stringify(payload)
      });

      const code = res.status;
      const data = await res.text();
      if (code >= 300) {
        return new Response(`HTTP ${code}\n${data}`, { headers: corsHeaders, status: 200 });
      }
      const j = JSON.parse(data);
      const out = j?.choices?.[0]?.message?.content ?? "ไม่มีคำตอบจาก AI";
      return new Response(out, { headers: corsHeaders });
    } catch (e) {
      return new Response(`error: ${e}`, { headers: corsHeaders, status: 200 });
    }
  }
}
