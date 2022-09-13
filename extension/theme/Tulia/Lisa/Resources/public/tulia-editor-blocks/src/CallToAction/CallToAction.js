const Editor = require('./Editor.vue').default;
const Render = require('./Render.vue').default;
const Manager = require('./Manager.vue').default;

export default {
    code: 'CallToAction',
    name: 'Call to action',
    icon: 'data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAPoAAABuCAMAAAA5xf5aAAAAwFBMVEU+V7b////+/+9Yjuh+a7dhm+xJadLc/v9/xPn++vP9++j759pCVse4lbtTWLZJZr1qYLaGx/pEYclMWrfp/f/a9f/+//b53dZyY7by/v/C9/6z6P1qpe1LbsRndL2QgLzt9Pj99eizxeXq3eNMcdhWb71nWbbOqsSrk8FYZLfN/P+P0vyDtOniwMqggbjH7/vK2O/Mw9ip0vZUgN710dH27Omeqdtsktpbfcy2pcvCnsB0tvS+ttGXlch7peTdz9dgfLyOAAADPElEQVR42u2ba1faQBCGZ02hYLVRLqGpQCCIAgoiCuL9//+rzmRN94RcKoeGc5q+zxeyJCT77MzOLh9CAAAAAAAAAAAAAAAAAAAAIH9sv6eKQc+3aSt8VRx82oqixFzo0VaoIgF1qEMd6lCHOtSLA9ShDnWoQx3q/5Z6dTCfz3sqnYKqV1+6FNC5bqoUiql+O6bf1K5dlUgR1asjHfD1ekiClRz4v6k+eOVH2Q8LFeHgC1lKHVP5q9qFe6L2J81nQawD3+WLxP+uma+680gflNxM9b5XP93C2RkNS1upH4eBNilgubmqXxLV3ubzW4/a2VFv1Wkb9YMfJOp9359/Nj2o7UY7RpU81b99p/KNzvtmDuqfpvrFBNmkQbmZo/oT0ZVpVR+71HnrndBzRF0PkXD0k+twl6jzYDolX9gPNzOy9B1kaeqxiXDFv5PQLV/HRGspJ4d88/cuN842g167URH6J0Sn+ak7M6pNjLlHRDZ16lTZVF92pRYO1xNH5mSdO3pmRk9+VBuTJcHTZytecPlwodVbY25JRks2BEdiuhGCUsJctHJTF8FzU8Kn3LmGGshopya8XOOqwZjuGuGUkVLc96TvkqS8Hi9XzTDhRV0GpHwWVK4rua885DI6kQ88ObdBi7hz+1F3ZroxzVL/uOaSjiTs5ogdS5yjYZwi6i3tKWeDJzb4+ILakcrAd5GtnB5QZySzZnDBKbmfhBdbLZmmbq65D6Mkl0xCVRZ9TlCf6tFxPLbWN+ApbxJcN2moi0nYZPk6N3Mvc7urH+6mfkGMUTfN/SxumQmvJbITXoQsNzvh06K+V3WzpbnWW5rMMiez02r8qcw9mTLn8ZjGy1yKujNLVOeH7GkjGy5ucXVhJD2bOPJhxxc3W9RFMqASjCmrRhe3kpuizuOSpG419vb3pfo45C3Ne3Sum97V6XySuqXhBDdbGukyF+zaIralSVFX90nqFRWneH9a+ycxddn1xCmeuppG1NODXkB19T6WQE/08iCHyeZFVFfV29Vq8VF/Viu/oRIppPomUIc61KEOdahDHepQh7oqElCHOtShjjee8J7bf/52IwAAAAAAAAAAAAAAAAAAwC78Al1Rc4Jb1Vv+AAAAAElFTkSuQmCC',
    manager: Manager,
    editor: Editor,
    render: Render,
    defaults: {
        content: 'Vivamus non sapien eu nibh semper dignissim a non purus.',
        bgIcon: 'fas fa-credit-card',
        btn: {
            icon: 'fas fa-headphones',
            label: 'Call to Action'
        }
    }
};
