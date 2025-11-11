<##
.SYNOPSIS
    Batch-optimizes Saffron Restaurant website images.

.DESCRIPTION
    - Creates a backup copy of original images before processing.
    - Generates optimized WebP derivatives with size-appropriate resizing.
    - Supports hero/slider, gallery, and generic assets with different presets.

.REQUIREMENTS
    - ImageMagick 7+ with WebP support (https://imagemagick.org).
      Ensure that the `magick` command is available in your PATH.

.USAGE
    # Dry-run (no optimization, just preview commands)
    .\scripts\optimize-images.ps1 -DryRun

    # Execute optimization
    .\scripts\optimize-images.ps1

.PARAMETER CsvPath
    Path to the image inventory CSV (default: image-inventory-with-category.csv).

.PARAMETER SourceRoot
    Root directory that contains the images (default: current repo root).

.PARAMETER BackupFolder
    Folder where originals will be copied before optimization (default: img\_originals).

.PARAMETER OutputFolder
    Folder where optimized WebP images will be written (default: img\optimized).

.PARAMETER DryRun
    When specified, only prints the commands that would run without modifying files.
##>
param(
    [string]$CsvPath = 'image-inventory-with-category.csv',
    [string]$SourceRoot = '.',
    [string]$BackupFolder = 'img\_originals',
    [string]$OutputFolder = 'img\optimized',
    [switch]$DryRun
)

function Test-CommandExists {
    param([string]$Command)
    try {
        Get-Command $Command -ErrorAction Stop | Out-Null
        return $true
    } catch {
        return $false
    }
}

if (-not (Test-CommandExists -Command 'magick')) {
    Write-Error 'ImageMagick (magick) is required but not found on PATH. Install from https://imagemagick.org and retry.'
    exit 1
}

if (-not (Test-Path $CsvPath)) {
    Write-Error "CSV inventory not found at $CsvPath. Generate it first with the provided PowerShell command."
    exit 1
}

$sourceRootFull = (Resolve-Path $SourceRoot).Path
$backupFull = Join-Path $sourceRootFull $BackupFolder
$outputFull = Join-Path $sourceRootFull $OutputFolder

if (-not (Test-Path $backupFull)) {
    New-Item -ItemType Directory -Path $backupFull | Out-Null
}

if (-not (Test-Path $outputFull)) {
    New-Item -ItemType Directory -Path $outputFull | Out-Null
}

$inventory = Import-Csv $CsvPath

$heroPatterns = @('img\slider\', 'css\color\img\slider\')
$galleryPatterns = @('img\dish\', 'img\chef\', 'img\special', 'img\gallery\')

foreach ($row in $inventory) {
    $rawPath = $row.Path
    if ([string]::IsNullOrWhiteSpace($rawPath)) {
        continue
    }

    $relativePath = if ([IO.Path]::IsPathRooted($rawPath)) {
        $normalizedRoot = $sourceRootFull.TrimEnd('\') + [IO.Path]::DirectorySeparatorChar
        if ($rawPath.StartsWith($normalizedRoot, [System.StringComparison]::OrdinalIgnoreCase)) {
            $rawPath.Substring($normalizedRoot.Length)
        } else {
            $rawPath
        }
    } else {
        $rawPath
    }

    $sourcePath = if ([IO.Path]::IsPathRooted($relativePath)) {
        $relativePath
    } else {
        Join-Path $sourceRootFull $relativePath
    }

    if (-not (Test-Path $sourcePath)) {
        Write-Warning "Skipping missing file: $relativePath"
        continue
    }

    $extension = [IO.Path]::GetExtension($sourcePath).ToLowerInvariant()
    if ($extension -notin @('.png', '.jpg', '.jpeg', '.gif', '.bmp')) {
        continue
    }

    $relativeDirectory = [IO.Path]::GetDirectoryName($relativePath)
    $targetFolder = if ([string]::IsNullOrEmpty($relativeDirectory)) {
        $outputFull
    } else {
        Join-Path $outputFull $relativeDirectory
    }
    if (-not (Test-Path $targetFolder)) {
        New-Item -ItemType Directory -Path $targetFolder -Force | Out-Null
    }

    $targetRelativePath = [IO.Path]::ChangeExtension($relativePath, '.webp')
    $targetPath = Join-Path $outputFull $targetRelativePath

    $backupTargetPath = Join-Path $backupFull $relativePath
    $backupTargetDir = [IO.Path]::GetDirectoryName($backupTargetPath)
    if (-not [string]::IsNullOrEmpty($backupTargetDir) -and -not (Test-Path $backupTargetDir)) {
        New-Item -ItemType Directory -Path $backupTargetDir -Force | Out-Null
    }

    if (-not (Test-Path $backupTargetPath)) {
        Copy-Item $sourcePath $backupTargetPath
    }

    $relativePathNormalized = $relativePath.Replace('/', '\')

    $quality = 80
    $resizeWidth = 1200

    if ($heroPatterns | Where-Object { $relativePathNormalized.StartsWith($_, [System.StringComparison]::OrdinalIgnoreCase) }) {
        $quality = 70
        $resizeWidth = 1600
    } elseif ($galleryPatterns | Where-Object { $relativePathNormalized.StartsWith($_, [System.StringComparison]::OrdinalIgnoreCase) }) {
        $quality = 75
        $resizeWidth = 800
    }

    $arguments = @()
    $arguments += $sourcePath
    if ($resizeWidth) {
        $arguments += '-resize'
        $arguments += "${resizeWidth}x>"
    }
    $arguments += '-strip'
    $arguments += '-quality'
    $arguments += $quality
    $arguments += '-define'
    $arguments += 'webp:method=6'
    $targetDir = [IO.Path]::GetDirectoryName($targetPath)
    if (-not (Test-Path $targetDir)) {
        New-Item -ItemType Directory -Path $targetDir -Force | Out-Null
    }
    $arguments += $targetPath

    $quotedArgs = $arguments | ForEach-Object { '"' + $_ + '"' }
    $command = 'magick ' + ($quotedArgs -join ' ')

    if ($DryRun) {
        Write-Output $command
    } else {
        Write-Output "Optimizing: $relativePath -> $targetRelativePath"
        & magick @arguments
    }
}

if (-not $DryRun) {
    Write-Output "Optimization complete! Review files under $OutputFolder and replace originals after QA."
}
