export const separator = "##!!##";

export function tokenFieldHandler(token) {
  return token.map((t) => {
    const field = t.split(separator);
    if (field.length === 1) {
      return field[0];
    } else if (field.length === 2) {
      return field[1];
    }
    return undefined;
  });
}

export function displayTransform(token) {
  const field = token.split(separator);
  return field[0] ?? "";
}
