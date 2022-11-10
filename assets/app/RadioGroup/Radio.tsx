import React from 'react'

interface Props {
  name: string,
  label: string,
  value: string,
  required: boolean,
  state: string,
  onChange: (event: React.ChangeEvent<HTMLInputElement>) => void
}

export default function Radio({name, label, value, required, state, onChange}: Props) {
  return <label
  className={`radio${required ? ' required' : ''}`}
>
  <input
    type="radio"
    name={name}
    required={required}
    value={value}
    checked={value === state}
    onChange={onChange}
    className="custom-radio"
  />
  <span className="icons">
    <span className="icon-unchecked"></span>
    <span className="icon-checked"></span>
  </span>
  <span>{label}</span>
</label>
}
