/**
 * เครื่องคิดภาษีย่อ (ประมาณการ) — ตัวเลขละเอียดคำนวณใน FAmiCal (/tax)
 * อัตราภาษีเงินได้บุคคลธรรมดาแบบขั้นบันได
 */
(function () {
	'use strict';

	var BRACKETS = [
		[150000, 0], [300000, 0.05], [500000, 0.10], [750000, 0.15],
		[1000000, 0.20], [2000000, 0.25], [5000000, 0.30], [Infinity, 0.35]
	];

	function parseMoney(v) {
		var n = Number(String(v || '').replace(/[^\d.]/g, ''));
		return isFinite(n) && n > 0 ? Math.round(n) : 0;
	}

	function fmt(n) {
		return '฿' + Math.round(n).toLocaleString('th-TH');
	}

	function estimate(monthly) {
		var annual = monthly * 12;
		var expense = Math.min(annual * 0.5, 100000);
		var personal = 60000;
		// ประกันสังคม ม.33 ปี 2569–2571: ฐานค่าจ้างสูงสุด 17,500 = จ่ายสูงสุด 875/เดือน (10,500/ปี) ลดหย่อนตามที่จ่ายจริง
		var SSO_WAGE_CAP = 17500;
		var sso = Math.min(monthly, SSO_WAGE_CAP) * 0.05 * 12;
		var net = Math.max(0, annual - expense - personal - sso);

		var tax = 0, lower = 0, rate = 0;
		for (var i = 0; i < BRACKETS.length; i++) {
			var upper = BRACKETS[i][0], r = BRACKETS[i][1];
			if (net > lower) {
				tax += (Math.min(net, upper) - lower) * r;
				rate = r;
			}
			lower = upper;
		}
		return { tax: tax, rate: rate };
	}

	document.querySelectorAll('[data-famical-tax-calc]').forEach(function (box) {
		var form = box.querySelector('form');
		var input = box.querySelector('input[name="salary"]');
		var result = box.querySelector('.tax-calc-result');
		var outTax = box.querySelector('[data-out="tax"]');
		var outRate = box.querySelector('[data-out="rate"]');
		var outHint = box.querySelector('[data-out="hint"]');
		if (!form || !input || !result) {
			return;
		}

		function update() {
			var monthly = parseMoney(input.value);
			if (!monthly) {
				result.hidden = true;
				return;
			}
			var e = estimate(monthly);
			result.hidden = false;
			outTax.textContent = fmt(e.tax);
			outRate.textContent = Math.round(e.rate * 100) + '%';
			outHint.textContent = e.rate > 0
				? 'ทุก 10,000 บาทที่ลดหย่อนเพิ่มได้ ช่วยประหยัดภาษีประมาณ ' + fmt(10000 * e.rate) + ' — ดูว่าควรลดหย่อนด้วยอะไรใน FAmiCal'
				: 'รายได้ระดับนี้ยังไม่ต้องเสียภาษี แต่ควรยื่นแบบทุกปี และวางแผนก่อนรายได้เพิ่ม';
		}

		input.addEventListener('input', update);

		// ส่งเฉพาะตัวเลข เช่น "35,000" -> 35000
		form.addEventListener('submit', function () {
			var monthly = parseMoney(input.value);
			if (monthly) {
				input.value = String(monthly);
			}
		});
	});
})();
