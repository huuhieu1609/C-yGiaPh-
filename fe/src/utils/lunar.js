// Ho Ngoc Duc's Lunar Calendar Algorithm translated to JavaScript (GMT+7 Timezone)

function jdFromDate(year, month, day) {
  const a = Math.floor((14 - month) / 12);
  const y = year + 4800 - a;
  const m = month + 12 * a - 3;
  return day + Math.floor((153 * m + 2) / 5) + 365 * y + Math.floor(y / 4) - Math.floor(y / 100) + Math.floor(y / 400) - 32045;
}

function getNewMoon(k) {
  const T = k / 1236.85;
  const Jd = 2415020.75933 + 29.53058868 * k
      + 0.0001178 * T * T
      - 0.000000155 * T * T * T
      + 0.00033 * Math.sin((166.56 + 132.87 * T - 0.009173 * T * T) * Math.PI / 180);
  
  const M = (359.2242 + 29.10535608 * k - 0.000033 * T * T - 0.000003 * T * T * T) * Math.PI / 180;
  const Mprime = (306.0253 + 385.81691806 * k + 0.01073 * T * T + 0.0000123 * T * T * T) * Math.PI / 180;
  const F = (21.2964 + 390.67050646 * k - 0.0238 * T * T - 0.000003 * T * T * T) * Math.PI / 180;
  
  const corr = (0.1734 - 0.000393 * T) * Math.sin(M)
      - 0.4068 * Math.sin(Mprime)
      + 0.0161 * Math.sin(2 * Mprime)
      - 0.0004 * Math.sin(3 * Mprime)
      + 0.0104 * Math.sin(2 * F)
      - 0.0051 * Math.sin(M + Mprime)
      - 0.0074 * Math.sin(M - Mprime)
      + 0.0004 * Math.sin(2 * F + M)
      - 0.0004 * Math.sin(2 * F - M)
      - 0.0006 * Math.sin(2 * F + Mprime)
      + 0.0010 * Math.sin(2 * F - Mprime)
      + 0.0005 * Math.sin(2 * M + Mprime);
      
  return Jd + corr + 7.0 / 24.0;
}

function getWinterSolstice(year) {
  return getSolarTerm(year, 24);
}

function getSolarTerm(year, index) {
  const y = year - 2000;
  const jdBase = 2451545.0 + 365.2422 * y;
  
  if (index === 24) {
    const days = 355.2422;
    return jdBase + days + 7.0/24.0;
  }
  
  const days = (index * 15.218);
  return jdBase + days + 7.0/24.0;
}

export function solarToLunar(sYear, sMonth, sDay) {
  const jdn = jdFromDate(sYear, sMonth, sDay);
  
  let k = Math.floor((jdn - 2415020.75933) / 29.530588853);
  let nm = getNewMoon(k);
  if (nm > jdn + 0.5) {
    k--;
    nm = getNewMoon(k);
  }
  
  const day = Math.floor(jdn - Math.floor(nm + 0.5) + 1);
  
  let lyYear = sYear;
  let ws = getWinterSolstice(sYear);
  let k11 = Math.floor((ws - 2415020.75933) / 29.530588853);
  let nm11 = getNewMoon(k11);
  if (nm11 > ws + 0.5) {
    k11--;
    nm11 = getNewMoon(k11);
  }
  
  if (jdn < nm11) {
    lyYear = sYear - 1;
    ws = getWinterSolstice(lyYear);
    k11 = Math.floor((ws - 2415020.75933) / 29.530588853);
    nm11 = getNewMoon(k11);
    if (nm11 > ws + 0.5) {
      k11--;
      nm11 = getNewMoon(k11);
    }
  }
  
  const diff = k - k11;
  
  let hasLeap = false;
  let leapMonthIndex = -1;
  
  const nextWs = getWinterSolstice(lyYear + 1);
  let kNext11 = Math.floor((nextWs - 2415020.75933) / 29.530588853);
  let nmNext11 = getNewMoon(kNext11);
  if (nmNext11 > nextWs + 0.5) {
    kNext11--;
    nmNext11 = getNewMoon(kNext11);
  }
  
  const totalMonths = kNext11 - k11;
  if (totalMonths === 13) {
    hasLeap = true;
    for (let i = 1; i <= 12; i++) {
      const nmStart = getNewMoon(k11 + i);
      const nmEnd = getNewMoon(k11 + i + 1);
      
      let hasTrungKhi = false;
      for (let tk = 0; tk <= 24; tk++) {
        const tkTime = getSolarTerm(lyYear, tk);
        if (tkTime >= nmStart && tkTime < nmEnd && tk % 2 === 0) {
          hasTrungKhi = true;
          break;
        }
      }
      if (!hasTrungKhi) {
        leapMonthIndex = i;
        break;
      }
    }
  }
  
  let month = 11 + diff;
  if (month > 12) month -= 12;
  if (month <= 0) month += 12;
  
  let isLeap = false;
  if (hasLeap && leapMonthIndex !== -1) {
    if (diff === leapMonthIndex) {
      isLeap = true;
      month = leapMonthIndex;
    } else if (diff > leapMonthIndex) {
      month = 11 + diff - 1;
      if (month > 12) month -= 12;
    }
  }
  
  const newYearDiff = (hasLeap && leapMonthIndex <= 2) ? 3 : 2;
  const lunarYear = (diff >= newYearDiff) ? (lyYear + 1) : lyYear;
  
  return [day, month, lunarYear, isLeap];
}

export function lunarToSolar(lYear, lMonth, lDay, isLeap = false) {
  for (let year = lYear; year <= lYear + 1; year++) {
    const startJd = jdFromDate(year, 1, 1);
    const endJd = jdFromDate(year, 12, 31);
    
    for (let jd = startJd; jd <= endJd; jd++) {
      // Simple reverse lookup using julian day
      // convert jd to solar date
      const z = Math.floor(jd + 0.5);
      const f = jd + 0.5 - z;
      let r = z;
      if (z >= 2299161) {
        const alpha = Math.floor((z - 1867216.25) / 36524.25);
        r = z + 1 + alpha - Math.floor(alpha / 4);
      }
      const b = r + 1524;
      const c = Math.floor((b - 122.1) / 365.25);
      const d = Math.floor(365.25 * c);
      const e = Math.floor((b - d) / 30.6001);
      
      const sDay = b - d - Math.floor(30.6001 * e);
      const sMonth = e < 14 ? e - 1 : e - 13;
      const sYear = sMonth > 2 ? c - 4716 : c - 4715;
      
      const [ld, lm, ly, il] = solarToLunar(sYear, sMonth, sDay);
      if (ld === lDay && lm === lMonth && il === isLeap) {
        const mmStr = sMonth < 10 ? '0' + sMonth : sMonth;
        const ddStr = sDay < 10 ? '0' + sDay : sDay;
        return `${sYear}-${mmStr}-${ddStr}`;
      }
    }
  }
  return null;
}
