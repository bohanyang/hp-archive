import React from 'react'
import Radio from './Radio'

interface Props {
  name: string
  label: string
  values: { [value: string]: string }
  required: boolean
  state: string
  setState: (value: string) => void
}

export default function RadioGroup ({
  name,
  label,
  values,
  required,
  state,
  setState
}: Props) {
  function onChange (event: React.ChangeEvent<HTMLInputElement>) {
    setState(event.currentTarget.value)
  }
  return (
    <div className="form-group">
      <h5 className={`control-label${required ? ' required' : ''}`}>{label}</h5>
      <div>
        {Object.keys(values).map((value) => {
          return (
            <Radio
              key={value}
              name={name}
              label={values[value]}
              value={value}
              required={required}
              state={state}
              onChange={onChange}
            ></Radio>
          )
        })}
      </div>
    </div>
  )
}
